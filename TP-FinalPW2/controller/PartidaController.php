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
        $usuario_id = $_SESSION['usuario_id'] ?? null;
        if (!$usuario_id) {
            $this->redirectTo("login/show");
        }

        $partida_id = $this->model->crearPartida($usuario_id);

        // Redirigir a mostrar la primera pregunta
        $this->redirectTo("partida/mostrarPregunta?partida_id=$partida_id");
        exit;

    }

    public function responderPregunta()
    {
        $usuario_id = $_SESSION['usuario_id'] ?? null;
        $partida_id = $_POST['partida_id'] ?? null;
        $pregunta_id = $_POST['pregunta_id'] ?? null;
        $respuesta_usuario = $_POST['respuesta'] ?? null;

        if (!$partida_id || !$pregunta_id || !$respuesta_usuario) {
            $this->redirectTo("login/show");
        }

        if (!isset($_SESSION['juego_estado'])) {
            $this->redirectTo("partida/mostrarPregunta?partida_id=$partida_id");
        }

        $estado = $_SESSION['juego_estado'];

        if ($estado['pregunta_id'] != $pregunta_id || $estado['partida_id'] != $partida_id) {
            $this->redirectTo("partida/mostrarPregunta?partida_id=$partida_id");
        }

        if ($estado['respondida']) {
            $this->redirectTo("partida/mostrarPregunta?partida_id=$partida_id");
        }

        $tiempoTranscurrido = time() - $estado['pregunta_entregada_en'];
        if ($tiempoTranscurrido > 10) {
            $puntaje = $this->model->calcularPuntajeFinal($partida_id);

            unset($_SESSION['juego_estado']);

            $data = [
                'puntaje' => $puntaje,
                'gano' => false,
                'mensaje' => 'Se agotó el tiempo para responder',
                'es_correcta' => false,
                'respuesta_correcta' => $this->model->obtenerRespuestaCorrecta($pregunta_id)['texto'],
                'partida_id' => $partida_id,
                'pregunta_id' => $pregunta_id
            ];

            $this->view->render("postPregunta", $data);
            return;
        }

        $_SESSION['juego_estado']['respondida'] = true;

        $esCorrecta = $this->model->guardarRespuesta($partida_id, $pregunta_id, $respuesta_usuario, $usuario_id);

        // Verificamos si el usuario ganó
        $gano = $this->model->borrarPreguntasRespondidasSiCompletoLaTabla($usuario_id);

        // Calcular puntaje y respuesta correcta
        $puntaje = $this->model->calcularPuntajeFinal($partida_id);
        $respuestaCorrecta = $this->model->obtenerRespuestaCorrecta($pregunta_id)['texto'];

        // Limpiar estado para evitar doble respuesta
        unset($_SESSION['juego_estado']);

        // Armar datos para vista postPregunta
        $data = [
            'partida_id' => $partida_id,
            'pregunta_id' => $pregunta_id,
            'es_correcta' => $esCorrecta,
            'respuesta_correcta' => $esCorrecta ? null : $respuestaCorrecta,
            'puntaje' => $puntaje,
            'gano' => $gano
        ];

        $this->view->render("postPregunta", $data);
    }


    public function mostrarPregunta() {

        $partida_id = $_GET['partida_id'] ?? null;

        // Verificar si hay una pregunta pendiente en sesión
        if (
            isset($_SESSION['juego_estado']) &&
            $_SESSION['juego_estado']['partida_id'] == $partida_id &&
            $_SESSION['juego_estado']['respondida'] === false
        ) {
            // Si ya había una pregunta activa, se vuelve a mostrar
            $pregunta_id = $_SESSION['juego_estado']['pregunta_id'];
            $pregunta = $this->model->obtenerPreguntaPorId($pregunta_id);

            if (!$pregunta) {
                // No se encontró la pregunta
                unset($_SESSION['juego_estado']);
                header("Location: /lobby/show");
                exit();
            }

            $entregadaEn = $_SESSION['juego_estado']['pregunta_entregada_en'];
        } else {
            // Si no hay pregunta activa o ya fue respondida, obtener nueva
            $pregunta = $this->model->obtenerPreguntaAleatoriaNoRespondida($partida_id);

            if (!$pregunta) {
                // Si no hay más preguntas, fin de la partida
                $puntaje = $this->model->calcularPuntajeFinal($partida_id);

                $usuario_id = $_SESSION['usuario_id'] ?? null;
                if ($usuario_id) {
                    $this->model->borrarPreguntasRespondidasSiCompletoLaTabla($usuario_id);
                }

                $data = [
                    'puntaje' => $puntaje,
                    'gano' => true
                ];
                $this->view->render("postPregunta", $data);
                exit();
            }

            // Guardar en sesión
            $_SESSION['juego_estado'] = [
                'partida_id' => $partida_id,
                'pregunta_id' => $pregunta['id'],
                'pregunta_entregada_en' => time(),
                'respondida' => false,
            ];

            $entregadaEn = $_SESSION['juego_estado']['pregunta_entregada_en'];
        }

        // Calcular tiempo restante
        $tiempoLimite = 10;
        $ahora = time();
        $tiempoPasado = $ahora - $entregadaEn;
        $tiempoRestante = max(0, $tiempoLimite - $tiempoPasado);

        // Preparar datos para la vista
        $data = [
            'partida_id' => $partida_id,
            'pregunta_id' => $pregunta['id'],
            'categoria_color' => $pregunta['color'],
            'categoria_nombre' => $pregunta['nombre'],
            'pregunta' => $pregunta['texto'],
            'opciones' => $pregunta['respuestas'],
            'tiempo_restante' => $tiempoRestante
        ];

        $this->view->render("partida", $data);
    }

    public function reportarPregunta()
    {
        // Solo aceptar POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        $idPregunta = $_POST['pregunta_id'] ?? null;
        $usuario_id = $_SESSION['usuario_id'] ?? null;

        if (!$idPregunta || !$usuario_id) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            exit;
        }

        $this->model->reportarPregunta($idPregunta);

        echo json_encode(['success' => true]);
        exit;
    }



    private function redirectTo($str)
    {
        header("Location: /" . $str);
        exit();
    }

}