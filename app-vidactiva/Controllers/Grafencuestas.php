<?php
class Grafencuestas extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        sessionUser();
        getPermisos(8);
    }

    public function grafencuestas()
    {
        if (empty($_SESSION['permisosMod']['r_permiso'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $data['page_tag'] = "Gráficos de Encuestas";
        $data['page_title'] = "Gráficos Estadísticos";
        $data['page_name'] = "grafencuestas";
        $data['page_functions_js'] = "functions_grafencuestas.js";
        $this->views->getView($this, "grafencuestas", $data);
    }
}
