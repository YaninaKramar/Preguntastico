<?php

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
    private function validarDatos()
    {
       $usuarioIngresando= this->validarUsuarioExistente();
       if (isset($usuarioIngresando)){

           if ($_POST["usuario"]==$usuarioIngresando["nombre_usuario"]&&$_POST["password"]==$usuarioIngresando["contrasenia"]){
               $this->redirectTo("/login/success");
           }
           else{
               echo "usuario invalido!!";
           }
       }else{
           echo "usuario invalido!!";
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