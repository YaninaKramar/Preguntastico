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

    public function finDePartida()
    {
        $this->view->render("finPartida");
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
            // Datos faltantes: ir a login
            $this->redirectTo("login/show");
        }

        if (!isset($_SESSION['juego_estado'])) {
            // No hay pregunta en sesión, no se puede responder
            $this->redirectTo("partida/mostrarPregunta?partida_id=$partida_id");
        }

        $estado = $_SESSION['juego_estado'];

        // Validar que pregunta y partida coinciden con sesión
        if ($estado['pregunta_id'] != $pregunta_id || $estado['partida_id'] != $partida_id) {
            $this->redirectTo("partida/mostrarPregunta?partida_id=$partida_id");
        }

        // Validar que no haya respondido antes
        if ($estado['respondida']) {
            $this->redirectTo("partida/mostrarPregunta?partida_id=$partida_id");
        }

        // Validar tiempo
        $tiempoTranscurrido = time() - $estado['pregunta_entregada_en'];
        if ($tiempoTranscurrido > 10) {
            // Tiempo agotado
            $puntaje = $this->model->calcularPuntajeFinal($partida_id);

            $data = [
                'puntaje' => $puntaje,
                'gano' => false,
                'mensaje' => 'Se agotó el tiempo para responder'
            ];

            // Limpiar estado para evitar volver a responder
            unset($_SESSION['juego_estado']);

            $this->view->render("finPartida", $data);
            exit;
        }

        // Marcar pregunta como respondida en sesión
        $_SESSION['juego_estado']['respondida'] = true;

        $esCorrecta = $this->model->guardarRespuesta($partida_id, $pregunta_id, $respuesta_usuario,$usuario_id);

        $gano = $this->model->borrarPreguntasRespondidasSiCompletoLaTabla($usuario_id);

        if($gano){
            // Finalizar partida y mostrar resumen
            $puntaje = $this->model->calcularPuntajeFinal($partida_id);

            $_SESSION['ultimo_puntaje']= $puntaje;
            $data = [
                'puntaje' => $puntaje,
                'gano' => true
            ];

            $this->view->render("finPartida", $data);
        }
        // Limpiar estado para la próxima pregunta
        unset($_SESSION['juego_estado']);

        if ($esCorrecta) {
            // Redirigir a la siguiente pregunta
            $this->redirectTo("partida/mostrarPregunta?partida_id=$partida_id");
            exit;
        } else {
            // Finalizar partida y mostrar resumen
            $puntaje = $this->model->calcularPuntajeFinal($partida_id);
            $respuestaCorrecta = $this->model->obtenerRespuestaCorrecta($pregunta_id);

            $_SESSION['ultimo_puntaje']= $puntaje;
            $data = [
                'puntaje' => $puntaje,
                'respuesta_correcta' => $respuestaCorrecta['texto'],
                'gano' => false
            ];

            $this->view->render("finPartida", $data);
        }
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
                $this->view->render("finPartida", $data);
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


    private function redirectTo($str)
    {
        header("Location: /" . $str);
        exit();
    }

}