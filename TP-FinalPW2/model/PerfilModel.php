<?php

class PerfilModel {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function obtenerPerfilPorId($id) {
        $id=intval($id);
        $query = "SELECT nombre_usuario, nombre_completo, fecha_nac, ciudad, pais, foto_perfil 
              FROM usuario WHERE id = $id";
        $resultados = $this->db->query($query);
        return $resultados[0] ?? null;
    }

    public function obtenerPartidasPorUsuario($id) {
        $id=intval($id);
        $query = "SELECT fecha, puntaje_final FROM partida WHERE usuario_id = $id ORDER BY fecha DESC";
        return $this->db->query($query);
    }

    public function obtenerPuntajeTotal($id) {
        $id=intval($id);
        $query = "SELECT SUM(puntaje_final) AS total FROM partida WHERE usuario_id = $id";
        $result = $this->db->query($query);
        return $result["total"] ?? 0;
    }
}