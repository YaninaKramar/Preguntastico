<?php

class EditorController{
    private $model;
    private $view;

    public function __construct($editorModel, $viewer)
    {
        $this->model = $editorModel;
        $this->view  = $viewer;
    }

    private function checkAccess()
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario_rol'] != 2) {
            header('Location: /login/show');
            exit;
        }
    }
    public function listado()
    {
        $this->checkAccess();
        $preguntas = $this->model->getPreguntas();

        $this->view->render('editor', [
            'preguntas' => $preguntas,
        ]);
    }
    public function sugeridas()
    {
        $this->checkAccess();
        $preguntas = $this->model->getPreguntasSugeridas();
        echo $this->view->render('editorPreguntasSugeridas', ['preguntasSugeridas' => $preguntas,]);
    }

    public function aprobarSugerida()
    {
        $id = $_GET['id'];

        $this->checkAccess();
        $this->model->aprobarSugerida($id);
        header('Location: /editor/sugeridas');
        exit;
    }

    public function rechazarSugerida()
    {
        $id = $_GET['id'];

        $this->checkAccess();
        $this->model->rechazarSugerida($id);
        header('Location: /editor/sugeridas');
        exit;
    }
    public function reportadas()
    {
        $this->checkAccess();
        $preguntas = $this->model->getPreguntasReportadas();
        echo $this->view->render('editorPreguntasReportadas', [
            'preguntasReportadas' => $preguntas,
        ]);
    }

    public function aprobarReporte()
    {
        $id = $_GET['id'];

        $this->checkAccess();
        $this->model->aprobarReporte($id);
        header('Location: /editor/reportadas');
        exit;
    }

    public function darBajaReporte()
    {
        $id = $_GET['id'];

        $this->checkAccess();
        $this->model->darBajaReportada($id);
        header('Location: /editor/reportadas');
        exit;
    }
    public function altaPregunta()
    {
        $this->checkAccess();
        echo $this->view->render('editorDarDeAltaPregunta', ['numeros' => [0, 1, 2, 3]]);
    }

    public function guardarPreguntaNueva()
    {
        $this->checkAccess();

        $texto = $_POST['texto'] ?? '';
        $categoria_id = $_POST['categoria_id'] ?? '';
        $incorrecta1 = $_POST['incorrecta1'] ?? '';
        $incorrecta2 = $_POST['incorrecta2'] ?? '';
        $incorrecta3 = $_POST['incorrecta3'] ?? '';
        $correcta = $_POST['correcta'] ?? '';

        // Validar datos mínimos
        if (!$texto || !$categoria_id || !$incorrecta1 || !$incorrecta2 || !$incorrecta3 || !$correcta) {
            die("Faltan datos");
        }

        $opciones = [
            ['texto' => $incorrecta1, 'correcta' => 0],
            ['texto' => $incorrecta2, 'correcta' => 0],
            ['texto' => $incorrecta3, 'correcta' => 0],
            ['texto' => $correcta, 'correcta' => 1],
        ];

        // Llamar al modelo para crear la pregunta
        $this->model->crearPregunta($texto, (int)$categoria_id, $opciones);

        header("Location: /editor/listado");
        exit();
    }


    public function editarPregunta()
    {
        $id = $_GET['id'];

        $this->checkAccess();
        $preguntas = $this->model->getPreguntas();
        $pregunta  = array_values(array_filter($preguntas, function ($p) use ($id) {
            return $p['id'] == $id;
        }))[0] ?? null;
        echo $this->view->render('editorModificarPregunta', [
            'pregunta' => $pregunta,
        ]);
    }

    public function editarSubmit()
    {
        $this->checkAccess();
        $id = (int)($_POST['id'] ?? 0);
        $texto = $_POST['texto'] ?? '';
        $this->model->actualizarPregunta($id, $texto);
        header('Location: /editor/listado');
        exit();
    }


    public function eliminar()
    {
        $id = $_GET['id'];

        $this->checkAccess();
        $this->model->eliminarPregunta($id);
        header('Location: /editor/listado');
        exit();
    }
}
?>
