<?php

class PartidaController
{
    private $model;
    private $view;

    public function __construct($model, $view)
    {
        $this->model = $model;
        $this->view = $view;
    }

    public function show()
    {
        $this->view->render("partida");
    }

    public function iniciarPartida()
    {
        session_start();
        $usuario_id = $_SESSION['usuario_id'] ?? null;

        if (!$usuario_id) {
            header("Location: index.php?controller=login&method=show");
            exit;
        }

        $partida_id = $this->model->crearPartida($usuario_id);

        // Redirigir a mostrar la primera pregunta
        header("Location: index.php?controller=partida&method=mostrarPregunta&partida_id=$partida_id");
        exit;
    }


    public function mostrarPregunta() {

        $partida_id = $_GET['partida_id'] ?? null;

        $pregunta = $this->model->obtenerPreguntaAleatoriaNoRespondida($partida_id);

        if (!$pregunta) {
            // No hay más preguntas → fin de la partida
            header("Location: index.php?action=finPartida&partida_id=$partida_id");
            exit;
        }

        // Preparar datos para la vista
        $data = [
            'partida_id' => $partida_id,
            'categoria_color' => $pregunta['color'],
            'categoria_nombre' => $pregunta['nombre'],
            'pregunta' => $pregunta['texto'],
            'opciones' => $pregunta['respuestas']  // aquí va el array con las 4 opciones
        ];

        $this->view->render("partida", $data);
    }

}