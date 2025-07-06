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
    public function validarDatos()
    {
        $usuarioIngresando = $this->validarUsuarioExistente();

        if ($usuarioIngresando) {
            $rol_id = $usuarioIngresando['id_rol'];
            $contrasenaIngresada = $_POST["password"];
            $contrasenaGuardada = $usuarioIngresando["contrasena"];

            $esValida = false;

            if ($rol_id == 3) {
                // Usuario común: contraseña hasheada
                $esValida = password_verify($contrasenaIngresada, $contrasenaGuardada);
            } else {
                // Admin/editor: contraseña en texto plano
                $esValida = $contrasenaIngresada === $contrasenaGuardada;
            }

            if ($esValida) {
                if (strtolower($usuarioIngresando['status']) !== 'activo') {
                    $this->redirectTo("registro/success");
                }

                $_SESSION['usuario_id'] = $usuarioIngresando['id'];
                $_SESSION['usuario'] = $usuarioIngresando['nombre_completo'];
                $_SESSION['usuario_rol'] = $usuarioIngresando['id_rol'];

                $this->redirectTo("login/success");
            } else {
                $_SESSION['error_contrasena'] = "La contraseña es incorrecta.";
                $this->redirectTo("login/show");
            }
        } else {
            $_SESSION['error_usuario'] = "El usuario no existe.";
            $this->redirectTo("login/show");
        }
    }


    public function logout(){
        session_unset();
        session_destroy();
        $this->redirectTo("login/show");
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
        $usuario = $_SESSION['usuario'] ?? 'Invitado';
        $rol = $_SESSION['usuario_rol'] ?? 3;

        switch ($rol) {
            case 1:
                // Admin
                $this->view->render("admin", ['usuario' => $usuario]);
                break;
            case 2:
                // Editor
                header("Location: /editor/listado");
                break;
            case 3:
            default:
                // Usuario común
                $this->view->render("lobby", ['usuario' => $usuario]);
                break;
        }
    }


    private function redirectTo($str)
    {
        header("Location: /" . $str);
        exit();
    }

}