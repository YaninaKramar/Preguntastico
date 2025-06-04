<?php
class PerfilController {
    private $model;
    private $view;

    public function __construct($perfilModel, $viewer) {
        $this->model = $perfilModel;
        $this->view = $viewer;
    }

    public function miPerfil() {
        if (!isset($_SESSION['usuario'])) {
            header('Location: /Preguntastico/TP-FinalPW2/login/show');
            exit;
        }

        $id = $_SESSION['usuario_id'];
        $this->mostrarPerfil($id, "miPerfil");
    }

    public function show() {
        if (!isset($_SESSION['usuario'])) {
            header('Location: /Preguntastico/TP-FinalPW2/login/show');
            exit;
        }

        if (!isset($_GET["id"])) {
            echo "ID de usuario no especificado.";
            return;
        }

        $id = $_GET["id"];
        $this->mostrarPerfil($id, "show?id=$id");
    }

    private function mostrarPerfil($id, $urlPath) {
        $usuario = $this->model->obtenerPerfilPorId($id);
        $partidas = $this->model->obtenerPartidasPorUsuario($id);
        $puntajeTotal = $this->model->obtenerPuntajeTotal($id);

        $qrData = urlencode("http://localhost/Preguntastico/TP-FinalPW2/perfil/$urlPath");
        $ciudadEncoded = urlencode($usuario["ciudad"]);
        $mapsUrl = "https://www.google.com/maps/search/?api=1&query={$ciudadEncoded}";

        $this->view->render("perfil", [
            "usuario" => $usuario,
            "partidas" => $partidas,
            "puntaje_total" => $puntajeTotal,
            "qr_url" => "https://api.qrserver.com/v1/create-qr-code/?data=$qrData&size=150x150",
            "maps_url" => $mapsUrl
        ]);
    }
}