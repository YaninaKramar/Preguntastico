<?php
class RegistroController
{
    private $model;
    private $view;

    public function __construct($model, $view)
    {
        $this->model = $model;
        $this->view = $view;
    }

    public function validarRegistro()
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
           $this->model->agregarUsuarioNuevo($nombre,$apellido,$pais,$provincia,$nacimiento,$sexo,$fotoDestino,$usuarioIngresado,$emailIngresado,$contrasenaIngresada, $idRol);
            $this->redirectTo("login/show");
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
        $this->view->render("login");
    }

    private function redirectTo($str)
    {
        header("Location: " . BASE_URL . ltrim($str, '/'));
        exit();
    }
}