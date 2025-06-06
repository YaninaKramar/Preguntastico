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
        $ultimo_puntaje= isset($_SESSION['ultimo_puntaje']) ? $_SESSION['ultimo_puntaje'] : '0';
        echo $this->view->render('lobby', ['usuario' => $usuario, 'ultimo_puntaje' => $ultimo_puntaje]);
    }


}