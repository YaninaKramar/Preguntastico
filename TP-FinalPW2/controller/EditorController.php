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
        if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'editor') {
            header('Location: /login/show');
            exit;
        }
    }
    public function listado()
    {
        $this->checkAccess();
        $preguntas = $this->model->getPreguntas();
        echo $this->view->render('editor_listado', [
            'preguntas' => $preguntas,
        ]);
    }
    public function sugeridas()
    {
        $this->checkAccess();
        $preguntas = $this->model->getPreguntasSugeridas();
        echo $this->view->render('editor_sugeridas', ['preguntasReportadas' => $preguntas,]);
    }

    public function aprobarSugerida(int $id)
    {
        $this->checkAccess();
        $this->model->aprobarSugerida($id);
        header('Location: /');
    }

    public function rechazarSugerida(int $id)
    {
        $this->checkAccess();
        $this->model->rechazarSugerida($id);
        header('Location: /');
    }
    public function reportadas()
    {
        $this->checkAccess();
        $preguntas = $this->model->getPreguntasReportadas();
        echo $this->view->render('editor_reportadas', [
            'preguntasReportadas' => $preguntas,
        ]);
    }

    public function aprobarReporte(int $id)
    {
        $this->checkAccess();
        $this->model->aprobarReporte($id);
        header('Location: /');
    }

    public function darBajaReporte(int $id)
    {
        $this->checkAccess();
        $this->model->darBajaReportada($id);
        header('Location: /');
    }
    public function altaForm()
    {
        $this->checkAccess();
        echo $this->view->render('editor_alta');
    }

    public function altaSubmit()
    {
        $this->checkAccess();
        $textoPregunta = $_POST['texto'] ?? '';
        $opciones      = $_POST['opciones'] ?? [];
        $this->model->crearPregunta($textoPregunta, $opciones);
        header('Location: /');
    }

    public function editarForm(int $id)
    {
        $this->checkAccess();
        $preguntas = $this->model->getPreguntas();
        $pregunta  = array_values(array_filter($preguntas, function ($p) use ($id) {
            return $p['id'] == $id;
        }))[0] ?? null;
        echo $this->view->render('editor_editar', [
            'pregunta' => $pregunta,
        ]);
    }

    public function editarSubmit()
    {
        $this->checkAccess();
        $id           = (int)($_POST['id'] ?? 0);
        $texto        = $_POST['texto'] ?? '';
        $opciones     = $_POST['opciones'] ?? [];
        $this->model->actualizarPregunta($id, $texto, $opciones);
        header('Location: /');
    }

    public function eliminar(int $id)
    {
        $this->checkAccess();
        $this->model->eliminarPregunta($id);
        header('Location: /');
    }
}
?>
