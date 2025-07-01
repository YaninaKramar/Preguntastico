<?php
class AdminController {
    private $model;
    private $view;

    public function __construct($adminModel, $viewer) {
        $this->model = $adminModel;
        $this->view = $viewer;
    }

    public function show() {
        if (!isset($_SESSION['usuario'])) {
            header('Location: /login/show');
            exit;
        }

        $filtro = $_POST['filtro'] ??'dia';



        $jugadores = $this->model->getCantidadJugadores();
        $partidas = $this->model->getCantidadPartidas($filtro);
        $preguntas_totales = $this->model->getCantidadPreguntas($filtro);
        $preguntas_creadas = $this->model->getCantidadPreguntasCreadas($filtro);
        $usuarios_nuevos = $this->model->getUsuariosNuevos($filtro);
        $usuarios_por_pais = $this->model->getUsuariosPorPais($filtro);
        $usuarios_por_sexo= $this->model->getUsuariosPorSexo($filtro);
        $usuarios_por_edad = $this->model->getUsuariosPorEdad($filtro);
        

        echo $this->view->render('admin', [
            'jugadores' => $jugadores,
            'partidas' => $partidas,
            'preguntas_totales' => $preguntas_totales,
            'preguntas_creadas' => $preguntas_creadas,
            'usuarios_nuevos' => $usuarios_nuevos,
            'usuarios_por_pais' => $usuarios_por_pais,
            'usuarios_por_sexo' => $usuarios_por_sexo,
            'usuarios_por_edad' => $usuarios_por_edad,
            'filtro' => $filtro
        ]);

    }
}