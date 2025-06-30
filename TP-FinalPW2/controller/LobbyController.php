<?php

class LobbyController
{
    private $model;
    private $view;

    public function __construct($lobbyModel, $view)
    {
        // Utilizo el model del perfil, ya que tiene el metodo que necesito (obtenerPartidasPorUsuario())
        $this->model = $lobbyModel;
        $this->view = $view;
    }

    public function show()
    {
        $usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Invitado';
        $id= isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : '0';

        $partidas = $this->model->obtenerPartidasPorUsuario($id);
        $ultimo_puntaje= $this->model->obtenerPuntajeTotal($id);
        echo $this->view->render('lobby',
            ['usuario' => $usuario,
                'ultimo_puntaje' => $ultimo_puntaje,
                "partidas" => $partidas]);
    }


}