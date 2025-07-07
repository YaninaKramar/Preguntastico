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
// PartidaController, PerfilController, LobbyController, AdminController = logueado y para cualquier rol

// Controladores que requieren estar logueado
$controladoresLogueado = ['partida', 'perfil', 'lobby', 'admin', 'editor'];

if (in_array($controllerName, $controladoresLogueado) && !$logueado) {
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
    header("Location: /lobby/show");
    exit();
}

