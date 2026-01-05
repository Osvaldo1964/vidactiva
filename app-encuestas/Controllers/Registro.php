<?php
class Registro extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        getPermisos(6); // Modulo ID 6 according to nav_admin
    }

    public function registro()
    {
        if (empty($_SESSION['permisosMod']['r_permiso'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $data['page_tag'] = "Registro de Respuestas";
        $data['page_title'] = "Registro de Encuestas";
        $data['page_name'] = "registro";
        $data['page_functions_js'] = "functions_registro.js";
        $this->views->getView($this, "registro", $data);
    }
}
