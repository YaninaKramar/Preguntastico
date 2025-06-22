<?php
class RankingModel {
    private $db;
    public function __construct($database) {
        $this->db = $database;
    }
    public function obtenerTopJugadores(int $limit = 10): array {
        $limit = intval($limit);
        $query = "SELECT u.nombre_usuario AS usuario, MAX(p.puntaje_final) AS puntos
          FROM partida p
          INNER JOIN usuario u ON p.usuario_id = u.id
          GROUP BY u.id
          ORDER BY puntos DESC
          LIMIT $limit";

        return $this->db->query($query);
    }
}
