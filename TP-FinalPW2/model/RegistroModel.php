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


    public function agregarUsuarioNuevo($nombre, $apellido, $pais, $provincia, $nacimiento, $sexo, $fotoPerfil, $usuarioIngresado, $emailIngresado, $contrasenaIngresada, $idRol) {
        $nombreCompleto = $nombre . ' ' . $apellido;


        $query = "INSERT INTO usuario (nombre_completo, fecha_nac, sexo, pais, ciudad, email, contrasena, nombre_usuario, foto_perfil, id_rol)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->database->prepare($query);

        if (!$stmt) {
            die("Error al preparar la consulta: " . $this->database->error);
        }

        $stmt->bind_param("sssssssssi",
            $nombreCompleto,
            $nacimiento,
            $sexo,
            $pais,
            $provincia,
            $emailIngresado,
            $contrasenaIngresada,
            $usuarioIngresado,
            $fotoPerfil,
            $idRol
        );

        if (!$stmt->execute()) {
            die("Error al ejecutar la consulta: " . $stmt->error);
        }

        $stmt->close();
        echo "usuario agregado";
    }



}