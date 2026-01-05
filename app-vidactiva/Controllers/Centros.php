<?php
class Centros extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        getPermisos(4); // Using a placeholder ID or specific one if known
    }

    public function centros()
    {
        if (empty($_SESSION['permisosMod']['r_permiso'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $data['page_tag'] = "Centros";
        $data['page_title'] = "Administración de Centros";
        $data['page_name'] = "centros";
        $data['page_functions_js'] = "functions_centros.js";
        $this->views->getView($this, "centros", $data);
    }


}
