<?php
class PerfilController {
    private $model;
    private $view;

    public function __construct($perfilModel, $viewer) {
        $this->model = $perfilModel;
        $this->view = $viewer;
    }

    public function show() {
        if (!isset($_SESSION['usuario'])) {
            header('Location: /login/show');
            exit;
        }

        $id = $_GET['id'] ?? $_SESSION['usuario_id'];

        $this->mostrarPerfil($id);
    }

    private function mostrarPerfil($id) {
        $usuario = $this->model->obtenerPerfilPorId($id);
        $partidas = $this->model->obtenerPartidasPorUsuario($id);
        $puntajeTotal = $this->model->obtenerPuntajeTotal($id);


        $this->view->render("perfil", [
            "id_usuario" => $id,
            "usuario" => $usuario,
            "partidas" => $partidas,
            "puntaje_total" => $puntajeTotal,
            "qr_url" => "/perfil/generarQr/id=$id",
        ]);
    }

    public function generarQr() {

        require_once(__DIR__ . '/../vendor/phpqrcode/qrlib.php');

        $idUsuario = $_GET['id'] ?? 0;
        if ($idUsuario == 0) {
            die("Falta el ID de usuario");
        }
        $urlPerfil = "http://localhost/perfil/show/id=$idUsuario";

        if (ob_get_contents()) ob_end_clean();
        header("Content-Type: image/png");
        QRcode::png($urlPerfil, false, QR_ECLEVEL_L, 6);
        exit;
    }

}