<?php
session_start();
require_once("Configuration.php");
$configuration = new Configuration();
$router = $configuration->getRouter();

$controllerName = $_GET["controller"] ?? "login";
$methodName = $_GET["method"] ?? "show";

$logueado = isset($_SESSION['usuario_id']);
$rol = $_SESSION['usuario_rol'] ?? null;

// Permisos por controlador
// LoginController, RegistroController = publico
// PartidaController, PerfilController, LobbyController, AdminController, EditorController = logueado
// Controladores que solo pueden acceder usuarios normales: PartidaController, PerfilController, Lobbycontroller, RankingController, SugerirPreguntaController
// Admins y editores no pueden jugar.

// Controladores que requieren estar logueado
$controladoresLogueado = ['partida', 'perfil', 'lobby', 'admin', 'editor'];
$controladoresUsuarios = ['partida', 'perfil', 'lobby', 'ranking', 'sugerirPregunta'];

if (in_array($controllerName, $controladoresLogueado) && !$logueado) {
    redirigirNoAutorizado();
}

if(in_array($controllerName, $controladoresUsuarios) && $rol != 3){
    redirigirNoAutorizado();
}

if($controllerName == "admin" && $rol != 1){
    redirigirNoAutorizado();
}

if($controllerName == "editor" && $rol != 2){
    redirigirNoAutorizado();
}

$router->go($controllerName, $methodName);

function redirigirNoAutorizado() {
    header("Location: /login/show");
    exit();
}

