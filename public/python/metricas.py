"""
Pipeline de métricas (se ejecuta en el navegador con Pyodide).

Entrada (la pone resources/js/admin-metricas.js):
    datos_json  texto JSON con las sesiones (ruta admin.metricas.datos)

Salida (la lee el JS):
    resultado   texto JSON con lo que debe mostrar la vista

Fases del enunciado que cubre este archivo:
    2. Exploración y limpieza de datos + métricas derivadas
    3. Visualización (matplotlib -> imágenes base64)          <- paso 5
    4. Modelo de predicción (scikit-learn)                     <- paso 6
"""
import base64
import io
import json

import matplotlib

matplotlib.use("Agg")  # dibuja en memoria, sin ventana
import matplotlib.pyplot as plt
import pandas as pd
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import (
    accuracy_score,
    confusion_matrix,
    precision_score,
    recall_score,
    roc_auc_score,
    roc_curve,
)
from sklearn.model_selection import train_test_split

# Contadores específicos del Juego 1. Las partidas anteriores a medirlos tienen NULL.
CONTADORES = ["final_score", "n_attempts", "errors_made", "help_clicks"]
ESENCIALES = ["username", "date_time", "session_length"]
MAX_SEGUNDOS = 24 * 60 * 60  # una sesión de más de 24 h no es real

# Colores de la web, para que los gráficos encajen con la vista
FONDO, TEXTO, SUAVE = "#10153f", "#d3dbea", "#93a0bd"
AZUL, NARANJA, VERDE = "#6f9bff", "#ff8a5c", "#8fcf8a"
plt.rcParams.update({
    "figure.facecolor": FONDO, "axes.facecolor": FONDO, "savefig.facecolor": FONDO,
    "axes.edgecolor": SUAVE, "axes.labelcolor": TEXTO, "axes.titlecolor": TEXTO,
    "text.color": TEXTO, "xtick.color": SUAVE, "ytick.color": SUAVE,
    "axes.grid": True, "grid.color": "#2d3548", "axes.axisbelow": True,
    "axes.titlesize": 11, "figure.figsize": (6, 3.6),
})


def redondear(valor, decimales=2):
    """Float normal de Python (o None si no hay valor), para poder pasarlo a JSON."""
    return None if pd.isna(valor) else round(float(valor), decimales)


# ---------------------------------------------------------------- fase 2: limpieza y métricas

def estructura(df):
    """df.info(): columnas, tipos y nulos."""
    return [
        {"nombre": c, "tipo": str(df[c].dtype), "nulos": int(df[c].isna().sum())}
        for c in df.columns
    ]


def limpiar(crudo):
    """Limpieza del enunciado: duplicados, nulos, anomalías y transformaciones.

    Devuelve el DataFrame limpio y un informe de lo que se ha hecho.
    """
    df = crudo.copy()
    informe = {"filas_entrada": int(len(df))}

    # 1. Sesiones duplicadas (mismo session_id)
    informe["duplicados"] = int(df.duplicated(subset="session_id").sum())
    df = df.drop_duplicates(subset="session_id", keep="first")

    # 2. Filas sin los datos imprescindibles (no se pueden usar). Una fecha ilegible cuenta como vacía.
    df["date_time"] = pd.to_datetime(df["date_time"], errors="coerce")
    informe["incompletas"] = int(df[ESENCIALES].isna().any(axis=1).sum())
    df = df.dropna(subset=ESENCIALES)

    # 3. Nulos de los contadores: las partidas antiguas no los medían, se interpretan como 0
    informe["nulos_rellenados"] = {c: int(df[c].isna().sum()) for c in CONTADORES}
    df[CONTADORES] = df[CONTADORES].fillna(0)

    # 4. Valores anómalos: duraciones absurdas, puntuaciones fuera de 0-100, contadores negativos
    anomala = (
        (df["session_length"] < 0)
        | (df["session_length"] > MAX_SEGUNDOS)
        | (df["final_score"] < 0)
        | (df["final_score"] > 100)
        | (df[["n_attempts", "errors_made", "help_clicks"]] < 0).any(axis=1)
    )
    informe["anomalias"] = int(anomala.sum())
    df = df[~anomala].copy()

    # 5. Tipos y transformaciones
    df = df.astype({"n_attempts": "int64", "errors_made": "int64", "help_clicks": "int64"})
    df["username"] = df["username"].astype(str).str.strip()
    df["session_minutes"] = df["session_length"] / 60  # segundos -> minutos

    informe["filas_salida"] = int(len(df))
    return df, informe


def agrupar_jugadores(df):
    """Una fila por jugador, con sus totales y la etiqueta returning_player."""
    por_jugador = (
        df.groupby("username")
        .agg(
            sessions=("session_id", "count"),
            first_session=("date_time", "min"),
            total_minutes=("session_minutes", "sum"),
            planets=("n_attempts", "sum"),        # planetas creados en total
            best_score=("final_score", "max"),
        )
        .reset_index()
    )
    # returning_player: 2 o más sesiones = 1 (vuelve), una sola sesión = 0 (abandona)
    por_jugador["returning_player"] = (por_jugador["sessions"] >= 2).astype(int)
    return por_jugador


def actividad_diaria(df):
    """Jugadores únicos activos por día (DAU), con 0 en los días sin actividad."""
    dau = df.groupby(df["date_time"].dt.normalize())["username"].nunique()
    return dau.reindex(pd.date_range(dau.index.min(), dau.index.max()), fill_value=0)


def metricas_derivadas(df, por_jugador):
    """Métricas derivadas del enunciado, calculadas sobre el DataFrame limpio."""
    player_count = int(len(por_jugador))
    retention_rate = por_jugador["returning_player"].mean() * 100
    dau = df.groupby(df["date_time"].dt.date)["username"].nunique()            # jugadores activos por día
    mau = df.groupby(df["date_time"].dt.to_period("M"))["username"].nunique()  # jugadores activos por mes

    resumen = {
        "player_count": player_count,
        "returning_players": int(por_jugador["returning_player"].sum()),
        "retention_rate": redondear(retention_rate, 1),
        "churn_rate": redondear(100 - retention_rate, 1),
        "average_session_length": redondear(df["session_minutes"].mean()),  # minutos
        "sessions_per_player": redondear(len(df) / player_count),
        "dau_medio": redondear(dau.mean(), 1),
        "mau_ultimo_mes": int(mau.iloc[-1]),
    }
    jugadores = [
        {
            "username": f["username"],
            "sessions": int(f["sessions"]),
            "first_session": f["first_session"].strftime("%Y-%m-%d"),
            "total_minutes": redondear(f["total_minutes"]),
            "returning_player": int(f["returning_player"]),
        }
        for _, f in por_jugador.sort_values(["sessions", "username"], ascending=[False, True]).iterrows()
    ]
    actividad = {
        "dau": [{"periodo": str(d), "jugadores": int(n)} for d, n in dau.items()],
        "mau": [{"periodo": str(p), "jugadores": int(n)} for p, n in mau.items()],
    }
    return resumen, jugadores, actividad


def describir(df):
    """df.describe() de las variables numéricas, en formato lista."""
    columnas = ["session_minutes"] + CONTADORES
    d = df[columnas].describe().T
    return [
        {
            "variable": nombre,
            "media": redondear(f["mean"]),
            "std": redondear(f["std"]),
            "min": redondear(f["min"]),
            "mediana": redondear(f["50%"]),
            "max": redondear(f["max"]),
        }
        for nombre, f in d.iterrows()
    ]


# ---------------------------------------------------------------- fase 3: gráficos

def a_base64(fig):
    """Convierte una figura de matplotlib en data:image/png;base64,... para incrustarla en el HTML."""
    buf = io.BytesIO()
    fig.savefig(buf, format="png", dpi=110, bbox_inches="tight")
    plt.close(fig)
    return "data:image/png;base64," + base64.b64encode(buf.getvalue()).decode("ascii")


def grafico_duracion(df, por_jugador):
    """¿Cómo se distribuye la duración de las sesiones?"""
    fig, ax = plt.subplots()
    ax.hist(df["session_minutes"], bins=min(12, max(3, len(df) // 2)), color=AZUL, edgecolor=FONDO)
    ax.set_title("Distribución de la duración de sesión")
    ax.set_xlabel("Minutos")
    ax.set_ylabel("Sesiones")
    return fig


def grafico_errores_ayudas(df, por_jugador):
    """¿Cómo se distribuyen los errores y las ayudas por sesión?"""
    fig, ejes = plt.subplots(1, 2, sharey=True, figsize=(8, 3.4))
    for ax, columna, color, etiqueta in [
        (ejes[0], "errors_made", NARANJA, "Errores (planetas no habitables)"),
        (ejes[1], "help_clicks", VERDE, "Ayudas (fichas consultadas)"),
    ]:
        ax.hist(df[columna], bins=range(0, int(df[columna].max()) + 2), color=color, edgecolor=FONDO, align="left")
        ax.set_xlabel(etiqueta)
        ax.xaxis.get_major_locator().set_params(integer=True)  # solo valores enteros en el eje
    ejes[0].set_ylabel("Sesiones")
    fig.suptitle("Distribución de errores y ayudas por sesión", fontsize=11)
    return fig


def grafico_puntuacion_jugador(df, por_jugador):
    """¿Qué mejor puntuación consigue cada jugador?"""
    tope = 15  # con muchos jugadores solo se dibujan los mejores, para que el gráfico no crezca sin límite
    datos = por_jugador.sort_values("best_score").tail(tope)
    fig, ax = plt.subplots(figsize=(6, max(2.4, 0.45 * len(datos) + 1)))
    ax.barh(datos["username"], datos["best_score"], color=AZUL)
    ax.set_title("Mejor puntuación por jugador" + (f" (los {tope} mejores)" if len(por_jugador) > tope else ""))
    ax.set_xlabel("Habitabilidad máxima (0 a 100)")
    ax.set_xlim(0, 100)
    return fig


def grafico_intentos_puntuacion(df, por_jugador):
    """¿Hay relación entre los intentos y la puntuación?"""
    fig, ax = plt.subplots()
    ax.scatter(df["n_attempts"], df["final_score"], color=AZUL, alpha=0.7, s=40)
    r = df["n_attempts"].corr(df["final_score"])
    ax.set_title("Intentos y puntuación" + ("" if pd.isna(r) else f" (r = {r:.2f})"))
    ax.set_xlabel("Planetas creados en la sesión")
    ax.set_ylabel("Puntuación")
    return fig


def grafico_correlaciones(df, por_jugador):
    """Matriz de correlaciones entre las variables numéricas."""
    columnas = ["session_minutes"] + CONTADORES
    corr = df[columnas].corr()
    fig, ax = plt.subplots(figsize=(5.4, 4.4))
    imagen = ax.imshow(corr, vmin=-1, vmax=1, cmap="coolwarm")
    ax.set_xticks(range(len(columnas)), columnas, rotation=40, ha="right")
    ax.set_yticks(range(len(columnas)), columnas)
    ax.grid(False)
    for i in range(len(columnas)):
        for j in range(len(columnas)):
            if not pd.isna(corr.iloc[i, j]):
                ax.text(j, i, f"{corr.iloc[i, j]:.2f}", ha="center", va="center", fontsize=8, color="black")
    fig.colorbar(imagen, ax=ax, fraction=0.046)
    ax.set_title("Correlaciones entre variables")
    return fig


def grafico_dau(df, por_jugador):
    """¿Cómo evoluciona la actividad en el tiempo? (jugadores activos por día)"""
    dau = actividad_diaria(df)
    fig, ax = plt.subplots()
    ax.plot(dau.index, dau.values, marker="o", color=AZUL)
    ax.set_title("Jugadores activos por día (DAU)")
    ax.set_ylabel("Jugadores")
    ax.set_ylim(bottom=0)
    ax.yaxis.get_major_locator().set_params(integer=True)
    fig.autofmt_xdate()
    return fig


def grafico_abandono(df, por_jugador):
    """¿En qué punto se queda cada jugador? El Juego 1 no tiene niveles: el progreso son los planetas creados."""
    tope = 8
    progreso = por_jugador["planets"].clip(upper=tope)
    categorias = list(range(0, int(progreso.max()) + 1))
    fig, ax = plt.subplots()
    base = [0] * len(categorias)
    for valor, etiqueta, color in [(1, "Vuelve (2 o más sesiones)", VERDE), (0, "Abandona (una sesión)", NARANJA)]:
        cuenta = [int(((progreso == c) & (por_jugador["returning_player"] == valor)).sum()) for c in categorias]
        ax.bar(categorias, cuenta, bottom=base, color=color, label=etiqueta, edgecolor=FONDO)
        base = [b + c for b, c in zip(base, cuenta)]
    ax.set_xticks(categorias, [f"{c}+" if c == tope else str(c) for c in categorias])
    ax.set_title("Planetas creados por jugador al dejar de jugar")
    ax.set_xlabel("Planetas creados en total")
    ax.set_ylabel("Jugadores")
    ax.yaxis.get_major_locator().set_params(integer=True)
    ax.legend(facecolor=FONDO, edgecolor=SUAVE, fontsize=8)
    return fig


GRAFICOS = [
    ("duracion", "Distribución de la duración de sesión", grafico_duracion),
    ("errores_ayudas", "Distribución de errores y ayudas", grafico_errores_ayudas),
    ("puntuacion_jugador", "Mejor puntuación por jugador", grafico_puntuacion_jugador),
    ("intentos_puntuacion", "Relación entre intentos y puntuación", grafico_intentos_puntuacion),
    ("correlaciones", "Correlaciones entre variables", grafico_correlaciones),
    ("dau", "Jugadores activos por día (DAU)", grafico_dau),
    ("abandono", "Punto en el que se queda cada jugador", grafico_abandono),
]


def generar_graficos(df, por_jugador):
    """Un gráfico que falle no impide ver los demás: se avisa con el campo `error`."""
    resultado_graficos = []
    for identificador, titulo, funcion in GRAFICOS:
        try:
            resultado_graficos.append({"id": identificador, "titulo": titulo, "imagen": a_base64(funcion(df, por_jugador))})
        except Exception as error:  # noqa: BLE001
            plt.close("all")
            resultado_graficos.append({"id": identificador, "titulo": titulo, "error": f"{type(error).__name__}: {error}"})
    return resultado_graficos


# ---------------------------------------------------------------- fase 4: modelo de predicción

# Variables de la PRIMERA sesión de cada jugador con las que se intenta predecir si vuelve
CARACTERISTICAS = ["session_minutes", "final_score", "n_attempts", "errors_made", "help_clicks"]
MIN_JUGADORES = 6    # con menos jugadores no hay forma de separar entrenamiento y prueba
MIN_POR_CLASE = 2    # hacen falta al menos 2 jugadores que vuelven y 2 que abandonan
NOMBRES_CLASE = ["Abandona", "Vuelve"]


def preparar_modelo(df, por_jugador):
    """Una fila por jugador: sus variables de la primera sesión (X) y si vuelve (y)."""
    primera = (
        df.sort_values(["date_time", "session_id"])
        .drop_duplicates("username", keep="first")
        .set_index("username")
    )
    X = primera[CARACTERISTICAS]
    y = por_jugador.set_index("username")["returning_player"].loc[X.index]
    return X, y


def grafico_matriz_confusion(matriz):
    fig, ax = plt.subplots(figsize=(4.4, 3.8))
    imagen = ax.imshow(matriz, cmap="Blues")
    ax.set_xticks([0, 1], NOMBRES_CLASE)
    ax.set_yticks([0, 1], NOMBRES_CLASE)
    ax.set_xlabel("Predicción del modelo")
    ax.set_ylabel("Realidad")
    ax.grid(False)
    for i in range(2):
        for j in range(2):
            ax.text(j, i, int(matriz[i][j]), ha="center", va="center", fontsize=16,
                    color="white" if matriz[i][j] > matriz.max() / 2 else "black")
    ax.set_title("Matriz de confusión (conjunto de prueba)")
    return fig


def grafico_roc(fpr, tpr, auc):
    fig, ax = plt.subplots(figsize=(4.4, 3.8))
    ax.plot(fpr, tpr, color=AZUL, lw=2, label=f"Modelo (AUC = {auc:.2f})")
    ax.plot([0, 1], [0, 1], color=SUAVE, ls="--", label="Azar")
    ax.set_xlabel("Falsos positivos")
    ax.set_ylabel("Verdaderos positivos")
    ax.set_title("Curva ROC")
    ax.legend(facecolor=FONDO, edgecolor=SUAVE, fontsize=8, loc="lower right")
    return fig


def grafico_importancias(importancias):
    datos = sorted(importancias, key=lambda x: x["importancia"])
    fig, ax = plt.subplots(figsize=(5.4, 3.4))
    ax.barh([d["variable"] for d in datos], [d["importancia"] for d in datos], color=VERDE)
    ax.set_title("Importancia de las variables")
    ax.set_xlabel("Importancia")
    return fig


def entrenar_modelo(df, por_jugador):
    """Entrena un clasificador para predecir returning_player a partir de la primera sesión."""
    X, y = preparar_modelo(df, por_jugador)
    clases = {"abandona": int((y == 0).sum()), "vuelve": int((y == 1).sum())}

    if len(y) < MIN_JUGADORES or min(clases.values()) < MIN_POR_CLASE:
        return {
            "entrenado": False,
            "jugadores": int(len(y)),
            "clases": clases,
            "minimos": {"jugadores": MIN_JUGADORES, "por_clase": MIN_POR_CLASE},
        }

    # 70 % entrenamiento / 30 % prueba, manteniendo la proporción de cada clase
    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.3, random_state=42, stratify=y)

    modelo = RandomForestClassifier(n_estimators=200, random_state=42, class_weight="balanced")
    modelo.fit(X_train, y_train)
    prediccion = modelo.predict(X_test)
    matriz = confusion_matrix(y_test, prediccion, labels=[0, 1])

    # ROC-AUC solo se puede calcular si el conjunto de prueba tiene las dos clases
    auc = fpr = tpr = None
    if y_test.nunique() == 2:
        probabilidad = modelo.predict_proba(X_test)[:, list(modelo.classes_).index(1)]
        auc = roc_auc_score(y_test, probabilidad)
        fpr, tpr, _ = roc_curve(y_test, probabilidad)

    importancias = [
        {"variable": v, "importancia": redondear(i, 3)}
        for v, i in zip(CARACTERISTICAS, modelo.feature_importances_)
    ]

    graficos = [("matriz", "Matriz de confusión", grafico_matriz_confusion(matriz))]
    if auc is not None:
        graficos.append(("roc", "Curva ROC", grafico_roc(fpr, tpr, auc)))
    graficos.append(("importancias", "Importancia de las variables", grafico_importancias(importancias)))

    return {
        "entrenado": True,
        "algoritmo": "RandomForestClassifier",
        "jugadores": int(len(y)),
        "clases": clases,
        "entrenamiento": int(len(y_train)),
        "prueba": int(len(y_test)),
        "caracteristicas": CARACTERISTICAS,
        "metricas": {
            "accuracy": redondear(accuracy_score(y_test, prediccion), 3),
            "precision": redondear(precision_score(y_test, prediccion, zero_division=0), 3),
            "recall": redondear(recall_score(y_test, prediccion, zero_division=0), 3),
            "roc_auc": redondear(auc, 3) if auc is not None else None,
        },
        "matriz": [[int(v) for v in fila] for fila in matriz],
        "importancias": importancias,
        "graficos": [
            {"id": i, "titulo": t, "imagen": a_base64(fig)} for i, t, fig in graficos
        ],
    }


# ---------------------------------------------------------------- orquestación

def analizar(datos):
    crudo = pd.read_json(io.StringIO(datos))
    if crudo.empty:
        return {"vacio": True}

    df, informe = limpiar(crudo)
    if df.empty:
        return {"vacio": True, "limpieza": informe, "estructura": estructura(crudo)}

    por_jugador = agrupar_jugadores(df)
    resumen, jugadores, actividad = metricas_derivadas(df, por_jugador)
    try:
        modelo = entrenar_modelo(df, por_jugador)
    except Exception as error:  # noqa: BLE001 - un fallo del modelo no debe tumbar el resto de la vista
        plt.close("all")
        modelo = {"entrenado": False, "error": f"{type(error).__name__}: {error}"}
    return {
        "vacio": False,
        "estructura": estructura(crudo),
        "limpieza": informe,
        "describe": describir(df),
        "resumen": resumen,
        "jugadores": jugadores,
        "actividad": actividad,
        "graficos": generar_graficos(df, por_jugador),
        "modelo": modelo,
    }


resultado = json.dumps(analizar(datos_json))
