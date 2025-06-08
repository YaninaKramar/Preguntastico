<?php
class RankingController {
    private $model;
    private $view;
    public function __construct($rankingModel, $viewer) {
        $this->model = $rankingModel;
        $this->view = $viewer;
    }

    public function show() {
        if (!isset($_SESSION['usuario'])) {
            header('Location: /Preguntastico/TP-FinalPW2/login/show');
            exit;
        }

        $jugadores = $this->model->obtenerTopJugadores(6);

        $topPlayers = array_slice($jugadores, 0, 3);
        $otherPlayers = array_slice($jugadores, 3);

        $data = [
            'topPlayers'   => $topPlayers,
            'otherPlayers' => $otherPlayers
        ];

        $this->view->render('ranking', $data);
    }
}
