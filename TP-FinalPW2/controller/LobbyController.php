<?php

class LobbyController
{
    private $view;

    public function __construct($view)
    {
        $this->view = $view;
    }

    public function show()
    {
        $usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Invitado';
        echo $this->view->render('lobby', ['usuario' => $usuario]);
    }


}