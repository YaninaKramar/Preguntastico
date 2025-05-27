<?php
session_start();

class LoginController
{
    private $model;
    private $view;

    public function __construct($model, $view)
    {
        $this->model = $model;
        $this->view = $view;
    }

    public function validarUsuarioExistente()
    {
        $usuarios = $this->model->getUsuarios();

        if (isset($_POST["usuario"])) {
            foreach ($usuarios as $usuario) {
                if ($usuario["nombre_usuario"] === ($_POST["usuario"])) {
                    return $usuario;
                }
            }
        }
        return null;
    }
    public function validarDatos()
    {
       $usuarioIngresando= $this->validarUsuarioExistente();
       if (isset($usuarioIngresando)){

           if($_POST["password"]==$usuarioIngresando["contrasena"]){
            $_SESSION['usuario']=$usuarioIngresando['nombe_completo'];
               $this->redirectTo("/Preguntastico/TP-FinalPW2/index.php?controller=Login&method=success");
               exit();
           }
           else{
               $_SESSION['error_contrasena']="La contraseña es incorrecta.";
               $this->redirectTo("/Preguntastico/TP-FinalPW2/index.php");
               exit();
           }
       }else{
           $_SESSION['error_usuario']="El usuario no existe.";
           $this->redirectTo("/Preguntastico/TP-FinalPW2/index.php");

       }
    }



    public function show()
    {
        $error_usuario=isset($_SESSION['error_usuario'])?$_SESSION['error_usuario']:null;
        $error_contrasena=isset($_SESSION['error_contrasena'])?$_SESSION['error_contrasena']:null;
        unset($_SESSION['error_usuario']);
        unset($_SESSION['error_contrasena']);
        $this->view->render("login",['error_usuario'=>$error_usuario,'error_contrasena'=>$error_contrasena]);
    }

    public function success()
    {
        $this->view->render("lobby");
    }

    private function redirectTo($str)
    {
        header("location:" . $str);
        exit();
    }

}