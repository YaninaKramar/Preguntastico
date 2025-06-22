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
        $nombre= $_POST["nombre"];
        $apellido= $_POST["apellido"];
        $pais=$_POST["pais"];
        $provincia=$_POST["provincia"];
        $nacimiento= $_POST["fecha"];
        $sexo= $_POST["sexo"];
        $usuarioIngresado= $this->validarUsuario();
        $emailIngresado= $this->validarEmail();
        $contrasenaIngresada= $this->validarContrasena();
        $idRol= 2;
        $latitud = $_POST["latitud"];
        $longitud = $_POST["longitud"];

        // Procesar imagen
        if ($_FILES["foto"]["error"] === UPLOAD_ERR_OK) {
            $fotoPerfil= basename($_FILES["foto"]["name"]);
            $fotoDestino = "uploads/" . $fotoPerfil;

            // Verifica que el directorio exista
            if (!is_dir("uploads")) {
                mkdir("uploads", 0777, true);
            }

            move_uploaded_file($_FILES["foto"]["tmp_name"], $fotoDestino);
        } else {
            $fotoDestino = "uploads/default.jpg"; // En caso de error o imagen opcional
        }

        if (isset($usuarioIngresado)&&isset($emailIngresado)&&isset($contrasenaIngresada)){
            $token = bin2hex(random_bytes(16));
           $this->model->agregarUsuarioNuevo($nombre,$apellido, $nacimiento,$sexo,$fotoDestino,$usuarioIngresado,$emailIngresado,$contrasenaIngresada, $token, $idRol, $latitud, $longitud);

           $idUsuario = $this->model->obtenerIdUsuario($usuarioIngresado);
           // Enviar email
            $body = $this->generateEmailBodyFor($usuarioIngresado, $token, $idUsuario);
            $this->emailSender->send($emailIngresado, $body);
           $this->redirectTo("registro/success");
         }else{
            $this->redirectTo("registro/show");
        }


    }

    public function validarEmail()
    {
        $emailIngresado= $_POST["email"];
        $usuariosExistentes= $this->model->getUsuarios();

        foreach ($usuariosExistentes as $usuario){
            if($emailIngresado==$usuario["email"]){
                $this->redirectTo("registro/show");
                return null;
            }
        }
        return $emailIngresado;

    }


    public function validarUsuario()
    {
        $usarioIngresado= $_POST["usuario"];
        $usuariosExistentes= $this->model->getUsuarios();

        foreach ($usuariosExistentes as $usuario){
            if($usarioIngresado==$usuario["nombre_usuario"]){
                $this->redirectTo("registro/show");
                return null;
            }
        }
        return $usarioIngresado;

    }


    public function validarContrasena()
    {
        $contrasenaIngresada = $_POST["contrasena"];
        $repContrasenaIngresada = $_POST["repContrasena"];

        if ($contrasenaIngresada !== $repContrasenaIngresada) {
            $this->redirectTo("registro/show");
            exit();
        }

        if (!preg_match('/[A-Z]/', $contrasenaIngresada) ||
            !preg_match('/[a-z]/', $contrasenaIngresada) ||
            !preg_match('/[0-9]/', $contrasenaIngresada) ||
            strlen($contrasenaIngresada) < 5) {
            $this->redirectTo("registro/show");
            exit();
        }
        return $contrasenaIngresada;
    }



    public function show()
    {
        $this->view->render("registro");
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