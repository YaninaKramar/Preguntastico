<?php

class SugerirPreguntaController
{
    private $model;
    private $view;

    public function __construct($model, $view)
    {
        $this->model = $model;
        $this->view  = $view;
    }

    public function show()
    {
        $categorias = $this->model->getCategorias();
        $this->view->render("sugerirPregunta", [
            'categorias' => $categorias,
        ]);
    }
    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectTo('');
        }

        $textoPregunta = trim($_POST['pregunta'] ?? '');
        $categoriaId   = (int)($_POST['categoria'] ?? 0);
        $opcionCorrecta = trim($_POST['correcta'] ?? '');
        $incorrectas   = [
            trim($_POST['incorrecta1'] ?? ''),
            trim($_POST['incorrecta2'] ?? ''),
            trim($_POST['incorrecta3'] ?? ''),
        ];

        if ($textoPregunta === '' || $categoriaId === 0 || $opcionCorrecta === '' || in_array('', $incorrectas, true)) {
            $_SESSION['error_form'] = 'Todos los campos son obligatorios';
            $this->redirectTo('pregunta/show');
        }

        $respuestas = [
            [$opcionCorrecta, true],
        ];
        foreach ($incorrectas as $inc) {
            $respuestas[] = [$inc, false];
        }

        $creadorId = $_SESSION['usuario_id'] ?? null;

        try {
            $this->model->crearPregunta($textoPregunta, $categoriaId, $creadorId, $respuestas);
            $_SESSION['exito_form'] = 'Pregunta sugerida con exito';
        } catch (Exception $e) {
            error_log($e->getMessage());
            $_SESSION['error_form'] = 'Ocurrio un error al guardar la pregunta.';
        }

        $this->redirectTo('lobby/show');
    }

    private function redirectTo(string $ruta)
    {
        header("Location: /$ruta");
        exit();
    }
}

?>
