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
        $nacimiento= $_POST["fecha"];
        $sexo= $_POST["sexo"];
        $usuarioIngresado= $this->validarUsuario();
        $emailIngresado= $this->validarEmail();
        $contrasenaIngresada= $this->validarContrasena();
        $idRol= 3;
        $latitud = $_POST["latitud"];
        $longitud = $_POST["longitud"];

        if ($_FILES["foto"]["error"] === UPLOAD_ERR_OK) {
            $fotoPerfil= basename($_FILES["foto"]["name"]);
            $fotoDestino = "uploads/" . $fotoPerfil;

            if (!is_dir("uploads")) {
                mkdir("uploads", 0777, true);
            }

            move_uploaded_file($_FILES["foto"]["tmp_name"], $fotoDestino);
        } else {
            $fotoDestino = "uploads/default.jpg"; 
        }

        if (isset($usuarioIngresado)&&isset($emailIngresado)&&isset($contrasenaIngresada)){
            $token = bin2hex(random_bytes(16));
           $this->model->agregarUsuarioNuevo($nombre,$apellido, $nacimiento,$sexo,$fotoDestino,$usuarioIngresado,$emailIngresado,$contrasenaIngresada, $token, $idRol, $latitud, $longitud, $pais);

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

    public function verificarEmailAjax()
    {
        header('Content-Type: application/json');
        $email = $_POST['email'] ?? '';
        $usuarios = $this->model->getUsuarios();

        $enUso = false;
        foreach ($usuarios as $usuario) {
            if (strtolower($usuario['email']) === strtolower($email)) {
                $enUso = true;
                break;
            }
        }

        echo json_encode(['enUso' => $enUso]);
        exit();
    }

    public function validarPasswordAjax()
    {
        $contrasena = $_POST['password'] ?? '';

        $errores = [];

        if (!preg_match('/[A-Z]/', $contrasena)) {
            $errores[] = "Debe contener al menos una letra mayúscula.";
        }
        if (!preg_match('/[a-z]/', $contrasena)) {
            $errores[] = "Debe contener al menos una letra minúscula.";
        }
        if (!preg_match('/[0-9]/', $contrasena)) {
            $errores[] = "Debe contener al menos un número.";
        }
        if (strlen($contrasena) < 5) {
            $errores[] = "Debe tener al menos 5 caracteres.";
        }

        header('Content-Type: application/json');
        echo json_encode([
            'valida' => empty($errores),
            'errores' => $errores
        ]);
        exit();
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

    }

    public function verificar(){

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