<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Usuario</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        button:hover {
            background-color: #005b8e;
            color: white;
        }
        body {
            background: linear-gradient(to bottom, #b4f1dd, #ffffff);
            min-height: 100vh;
            font-family: Arial, sans-serif;
        }
        .btn-rounded {
            border-radius: 20px;
            padding: 8px 20px;
        }
        .btn-darkblue {
            background-color: #00334e;
            color: white;
        }
        .highlight-box {
            border: 1px solid black;
            border-radius: 20px;
            padding: 15px;
            background-color: white;
        }
        .highlight-title {
            color: #ff6600;
            font-weight: bold;
            font-size: 20px;
        }
    </style>
</head>
<body class="p-4">

<div class="d-flex justify-content-between align-items-start mb-3">
    <button class="btn-darkblue btn-rounded">Mi Perfil</button>
    <h1 class="text-center flex-grow-1 text-warning fw-bold">Bienvenido <span class="text-warning">USUARIO!</span></h1>
    <button class="btn-darkblue btn-rounded">Cerrar Sesión</button>
</div>

<div class="text-center mb-4">
    <p class="fs-5">Tu puntaje actual<br><strong class="fs-4">es: PUNTAJE</strong></p>
</div>

<div class="row">
    <div class="col-md-3 d-flex flex-column gap-3">
        <button class="btn-darkblue btn-rounded">Ir al ranking mundial</button>
        <button class="btn-darkblue btn-rounded">Crear Nueva Pregunta</button>
    </div>

    <div class="col-md-6 text-center">
        <button class="btn-darkblue btn-rounded px-5 py-3 fs-5">Iniciar nueva partida</button>
    </div>

    <div class="col-md-3">
        <div class="highlight-box">
            <div class="highlight-title mb-2 text-center">Partidas jugadas</div>
            <ul class="list-unstyled mb-0">
                <li>Fecha: Puntos</li>
            </ul>
        </div>
    </div>
</div>

</body>
</html>
