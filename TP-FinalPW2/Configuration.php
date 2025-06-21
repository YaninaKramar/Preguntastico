<?php
require_once("core/Database.php");
require_once("core/FilePresenter.php");
require_once("core/MustachePresenter.php");
require_once("core/Router.php");
require_once("core/EmailSender.php");

require_once("controller/HomeController.php");
require_once("controller/LoginController.php");
require_once("controller/RegistroController.php");
require_once("controller/PartidaController.php");
require_once("controller/LobbyController.php");

require_once("model/LoginModel.php");
require_once("model/RegistroModel.php");
require_once("model/PartidaModel.php");

require_once("controller/PerfilController.php");
require_once("model/PerfilModel.php");

require_once("model/RankingModel.php");
require_once("controller/RankingController.php");

include_once('vendor/mustache/src/Mustache/Autoloader.php');

class Configuration
{
    public function getDatabase()
    {
        $config = $this->getIniConfig();

        return new Database(
            $config["database"]["server"],
            $config["database"]["user"],
            $config["database"]["dbname"],
            $config["database"]["pass"]
        );
    }

    public function getIniConfig()
    {
        return parse_ini_file("configuration/config.ini", true);
    }


    public function getHomeController()
    {
        return new HomeController($this->getViewer());
    }

    public function getLobbyController() {
        if (!isset($_SESSION['usuario'])) {
            header('Location: /login/show');
            exit;
        }
        return new LobbyController($this->getViewer());
    }
    public function getPartidaController()
    {
        return new PartidaController(new PartidaModel ($this->getDataBase()), $this->getViewer());
    }


    public function getLoginController(){
        return new LoginController(new LoginModel ($this->getDataBase()), $this->getViewer());
    }

    public function getRegistroController(){
        return new RegistroController(new RegistroModel ($this->getDataBase()), $this->getViewer(), new EmailSender());
    }
    public function getPerfilController() {
        return new PerfilController(new PerfilModel($this->getDatabase()), $this->getViewer());
    }
    public function getRankingController() {
        return new RankingController(new RankingModel($this->getDatabase()), $this->getViewer());
    }
    public function getRouter()
    {
        return new Router("getHomeController", "show", $this);
    }

    public function getViewer()
    {
        //return new FileView();
        return new MustachePresenter("view");
    }
}