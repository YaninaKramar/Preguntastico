<?php

class EditorController{
    private $model;
    private $view;

    public function __construct($editorModel, $viewer)
    {
        $this->model = $editorModel;
        $this->view  = $viewer;
    }

    private function checkAccess(): void
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'editor') {
            header('Location: /login/show');
            exit;
        }
    }
    public function listado(): void
    {
        $this->checkAccess();
        $preguntas = $this->model->getPreguntas();
        echo $this->view->render('editor_listado', [
            'preguntas' => $preguntas,
        ]);
    }
    public function sugeridas(): void
    {
        $this->checkAccess();
        $preguntas = $this->model->getPreguntasSugeridas();
        echo $this->view->render('editor_sugeridas', [
            'preguntasReportadas' => $preguntas, // la vista usa este nombre
        ]);
    }

    public function aprobarSugerida(int $id): void
    {
        $this->checkAccess();
        $this->model->aprobarSugerida($id);
        header('Location: /editor/sugeridas');
    }

    public function rechazarSugerida(int $id): void
    {
        $this->checkAccess();
        $this->model->rechazarSugerida($id);
        header('Location: /editor/sugeridas');
    }
    public function reportadas(): void
    {
        $this->checkAccess();
        $preguntas = $this->model->getPreguntasReportadas();
        echo $this->view->render('editor_reportadas', [
            'preguntasReportadas' => $preguntas,
        ]);
    }

    public function aprobarReporte(int $id): void
    {
        $this->checkAccess();
        $this->model->aprobarReporte($id);
        header('Location: /editor/reportadas');
    }

    public function darBajaReporte(int $id): void
    {
        $this->checkAccess();
        $this->model->darBajaReportada($id);
        header('Location: /editor/reportadas');
    }
    public function altaForm(): void
    {
        $this->checkAccess();
        echo $this->view->render('editor_alta');
    }

    public function altaSubmit(): void
    {
        $this->checkAccess();
        $textoPregunta = $_POST['texto'] ?? '';
        $opciones      = $_POST['opciones'] ?? []; // array con ['texto'=>..,'correcta'=>..]
        $this->model->crearPregunta($textoPregunta, $opciones);
        header('Location: /editor/listado');
    }

    public function editarForm(int $id): void
    {
        $this->checkAccess();
        // Reutilizamos listado para encontrar la pregunta concreta
        $preguntas = $this->model->getPreguntas();
        $pregunta  = array_values(array_filter($preguntas, function ($p) use ($id) {
            return $p['id'] == $id;
        }))[0] ?? null;
        echo $this->view->render('editor_editar', [
            'pregunta' => $pregunta,
        ]);
    }

    public function editarSubmit(): void
    {
        $this->checkAccess();
        $id           = (int)($_POST['id'] ?? 0);
        $texto        = $_POST['texto'] ?? '';
        $opciones     = $_POST['opciones'] ?? [];
        $this->model->actualizarPregunta($id, $texto, $opciones);
        header('Location: /editor/listado');
    }

    public function eliminar(int $id): void
    {
        $this->checkAccess();
        $this->model->eliminarPregunta($id);
        header('Location: /editor/listado');
    }
}
?>
