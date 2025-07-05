<?php

class RegistroModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function getUsuarios(){
        return  $this->database->query("SELECT * FROM usuario");
    }


    public function agregarUsuarioNuevo($nombre, $apellido, $nacimiento, $sexo, $fotoPerfil, $usuarioIngresado, $emailIngresado, $contrasenaIngresada, $token, $idRol, $latitud, $longitud,$pais) {
        $nombreCompleto = $nombre . ' ' . $apellido;

        // Hashear password
        $passWordHasheada = password_hash($contrasenaIngresada, PASSWORD_DEFAULT);

        $query = "INSERT INTO usuario (nombre_completo, fecha_nac, sexo, email, contrasena, nombre_usuario, foto_perfil, token, id_rol, latitud, longitud,pais,fecha_registro)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?,now())";

        $stmt = $this->database->prepare($query);

        if (!$stmt) {
            die("Error al preparar la consulta: " . $this->database->error);
        }

        $stmt->bind_param("ssssssssidds",
            $nombreCompleto,
            $nacimiento,
            $sexo,
            $emailIngresado,
            $passWordHasheada,
            $usuarioIngresado,
            $fotoPerfil,
            $token,
            $idRol,
            $latitud,
            $longitud,
            $pais
        );

        if (!$stmt->execute()) {
            die("Error al ejecutar la consulta: " . $stmt->error);
        }

        $stmt->close();
        echo "usuario agregado";
    }

    public function verificarToken($idUsuario, $token) {
        $query = "SELECT token FROM usuario WHERE id = ?";

        $stmt = $this->database->prepare($query);
        if (!$stmt) {
            die("Error al preparar la consulta: " . $this->database->error);
        }

        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $tokenGuardado = null;
        $stmt->bind_result($tokenGuardado);
        $stmt->fetch();
        $stmt->close();

        // Comparar tokens de forma segura
        return hash_equals($tokenGuardado, $token);
    }


    public function activarUsuario($idUsuario) {
        $query = "UPDATE usuario SET status = 'activo' WHERE id = ?";

        $stmt = $this->database->prepare($query);
        if (!$stmt) {
            die("Error al preparar la consulta: " . $this->database->error);
        }

        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $stmt->close();
    }

    public function obtenerIdUsuario($usuario) {
        $query = "SELECT id FROM usuario WHERE nombre_usuario = ?";

        $stmt = $this->database->prepare($query);
        if (!$stmt) {
            die("Error al preparar la consulta: " . $this->database->error);
        }

        $stmt->bind_param("s", $usuario);
        $stmt->execute();

        $id = null;
        $stmt->bind_result($id);

        if ($stmt->fetch()) {
            $stmt->close();
            return $id;
        } else {
            $stmt->close();
            return null;
        }
    }



}