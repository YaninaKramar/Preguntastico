<?php
session_start();
require_once("Configuration.php");
$configuration = new Configuration();
$router = $configuration->getRouter();

$controllerName = $_GET["controller"] ?? "login";
$methodName = $_GET["method"] ?? "show";

$logueado = isset($_SESSION['usuario_id']);

// Permisos por controlador
// LoginController, RegistroController = publico
// PartidaController, PerfilController, LobbyController = logueado y para cualquier rol

// Controladores que requieren estar logueado
$controladoresLogueado = ['partida', 'perfil', 'lobby'];

if (in_array($controllerName, $controladoresLogueado) && !$logueado) {
    redirigirNoAutorizado("login/show");
}

$router->go($controllerName, $methodName);

function redirigirNoAutorizado($str) {
    header("Location: /" . $str);
    exit();
}

