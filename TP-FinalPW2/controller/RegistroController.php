<?php
class RegistroController
{
    private $model;
    private $view;
    private $emailSender;

    public function __construct($model, $view, $emailSender)
    {
        $this->model = $model;
        $this->view = $view;
        $this->emailSender = $emailSender;
    }

    public function procesar()
    {
        $nombre = $_POST["nombre"];
        $apellido = $_POST["apellido"];
        $pais = $_POST["pais"];
        $nacimiento = $_POST["fecha"];
        $sexo = $_POST["sexo"];
        $idRol = 3;
        $latitud = $_POST["latitud"];
        $longitud = $_POST["longitud"];
        $pais = $_POST["pais"];

        // Procesar imagen
        if ($_FILES["foto"]["error"] === UPLOAD_ERR_OK) {
            $fotoPerfil = basename($_FILES["foto"]["name"]);
            $fotoDestino = "uploads/" . $fotoPerfil;

            // Verifica que el directorio exista
            if (!is_dir("uploads")) {
                mkdir("uploads", 0777, true);
            }

            move_uploaded_file($_FILES["foto"]["tmp_name"], $fotoDestino);
        } else {
            $fotoDestino = "uploads/default.jpg"; // En caso de error o imagen opcional
        }

        $usuarioIngresado = $this->validarUsuario();
        $emailIngresado = $this->validarEmail();
        $contrasenaIngresada = $this->validarContrasena();

        if ($usuarioIngresado != false && $emailIngresado != false && $contrasenaIngresada != false) {
            $token = bin2hex(random_bytes(16));
            $this->model->agregarUsuarioNuevo($nombre, $apellido, $nacimiento, $sexo, $fotoDestino, $usuarioIngresado, $emailIngresado, $contrasenaIngresada, $token, $idRol, $latitud, $longitud, $pais);

            $idUsuario = $this->model->obtenerIdUsuario($usuarioIngresado);
            // Enviar email
            $body = $this->generateEmailBodyFor($usuarioIngresado, $token, $idUsuario);
            $this->emailSender->send($emailIngresado, $body);
            $this->redirectTo("registro/success");
            if (isset($usuarioIngresado) && isset($emailIngresado) && isset($contrasenaIngresada)) {
                $this->model->agregarUsuarioNuevo($nombre, $apellido, $pais, $nacimiento, $sexo, $fotoDestino, $usuarioIngresado, $emailIngresado, $contrasenaIngresada, $idRol);
                $this->redirectTo("login/show");
            } else {
                $this->redirectTo("registro/show");
            }


        }
    }

    public function validarEmail()
    {
        $emailIngresado= $_POST["email"];
        $usuariosExistentes= $this->model->getUsuarios();

        if (empty($emailIngresado)) {
            $_SESSION['error_registro_email'] = "El email no puede estar vacío.";
            return false;
        }
        foreach ($usuariosExistentes as $usuario){
            if($emailIngresado==$usuario["email"]){
                $_SESSION['error_registro_email'] = "Este email ya está registrado.";
                return false;
            }
        }
        return $emailIngresado;

    }


    public function validarUsuario()
    {
        $usarioIngresado= $_POST["usuario"];
        $usuariosExistentes= $this->model->getUsuarios();

        if (empty($usuarioIngresado)) {
            $_SESSION['error_registro_usuario'] = "El nombre de usuario no puede estar vacío.";
            return false;
        }
        foreach ($usuariosExistentes as $usuario){
            if($usarioIngresado==$usuario["nombre_usuario"]){
                $_SESSION['error_registro_usuario'] = "Este nombre de usuario ya está en uso.";
                return false;
            }
        }
        return $usarioIngresado;

    }


    public function validarContrasena()
    {
        $contrasenaIngresada = $_POST["contrasena"];
        $repContrasenaIngresada = $_POST["repContrasena"];


        if (empty($contrasenaIngresada)) {
            $_SESSION['error_registro_contrasena'] = "La contraseña no puede estar vacía.";
            return false;
        }
        if ($contrasenaIngresada !== $repContrasenaIngresada) {
            $_SESSION['error_registro_contrasena'] = "Las contraseñas no coinciden.";
            $_SESSION['error_registro_repContrasena'] = "Las contraseñas no coinciden.";
            return false;
        }

        $passwordErrors = [];
        if (!preg_match('/[A-Z]/', $contrasenaIngresada)) {
            $passwordErrors[] = "una mayúscula";
        }
        if (!preg_match('/[a-z]/', $contrasenaIngresada)) {
            $passwordErrors[] = "una minúscula";
        }
        if (!preg_match('/[0-9]/', $contrasenaIngresada)) {
            $passwordErrors[] = "un número";
        }
        if (strlen($contrasenaIngresada) < 5) {
            $passwordErrors[] = "al menos 5 caracteres";
        }

        if (!empty($passwordErrors)) {
            $_SESSION['error_registro_contrasena'] = "La contraseña debe contener al menos: " . implode(", ", $passwordErrors) . ".";
            return false;
        }
        return $contrasenaIngresada;
    }



    public function show()
    {
        $error_contrasena = $_SESSION['error_registro_contrasena'] ?? null;
        $error_repContrasena = $_SESSION['error_registro_repContrasena'] ?? null;
        unset($_SESSION['error_registro_contrasena']);
        unset($_SESSION['error_registro_repContrasena']);

        $data = [
            'error_contrasena' => $error_contrasena,
            'error_repContrasena' => $error_repContrasena,
            'has_error_contrasena' => (isset($error_contrasena) && $error_contrasena !== null) || (isset($error_repContrasena) && $error_repContrasena !== null),
        ];

            $this->view->render("registro", $data);
    }

    public function success()
    {
        $this->view->render("registroSuccess");
        // Revisa tu correo www.preguntastico.com/registro/verificar/idVerificador=<random guardado en la base>&idUsuario=123
    }

    public function verificar(){
        // Si coincide el random, cambio el status a active/true
        // redirect al home o mensaje de exito

        $idVerificador = $_GET["idVerificador"];
        $idUsuario = $_GET["idUsuario"];

        $tokenValido = $this->model->verificarToken($idUsuario, $idVerificador);

        if ($tokenValido) {
            $this->model->activarUsuario($idUsuario); // actualiza campo 'status' a 'activo'
            $this->redirectTo("login/show");
        } else {
            echo "Token inválido o expirado.";
        }

    }

    private function redirectTo($str)
    {
        header("Location: /" . $str);
        exit();
    }

    private function generateEmailBodyFor($usuarioIngresado, $token, $idUsuario)
    {
        return "<body>Hola $usuarioIngresado, para validar tu cuenta <a href='http://localhost/registro/verificar/idUsuario=$idUsuario&idVerificador=$token'>hace click aca</a></body>";
    }

}