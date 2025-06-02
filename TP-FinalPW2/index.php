<?php
require_once("Configuration.php");
$configuration = new Configuration();
$router = $configuration->getRouter();
define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/');

$router->go(
    $_GET["controller"] ?? "login",
    $_GET["method"] ?? "show"
);