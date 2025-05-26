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
               $this->redirectTo("/Preguntastico/TP-FinalPW2/index.php");
           }
       }else{
           $this->redirectTo("/Preguntastico/TP-FinalPW2/index.php");

       }
    }



    public function show()
    {
        $this->view->render("login");
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