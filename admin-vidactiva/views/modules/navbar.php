<?php
/* Alertas */
//var_dump($_POST);
//echo '<pre>'; print_r($_SESSION); echo '</pre>';
$totalerts = 0;
$url = "movalerts?select=id_movalert,status_movalert&linkTo=status_movalert&equalTo=Activo";
$method = "GET";
$fields = array();
$alerts = CurlController::request($url, $method, $fields);
//echo '<pre>'; print_r($url); echo '</pre>';exit;
if ($alerts->status == 200) {
  $alerts = $alerts->total;
} else {
  $alerts = 0;
}

$totalerts = 0;
$url = "pqrs?select=id_pqr,status_pqr&linkTo=status_pqr&equalTo=Activo";
$method = "GET";
$fields = array();
$pqrs = CurlController::request($url, $method, $fields);
//echo '<pre>'; print_r($url); echo '</pre>';exit;
if ($pqrs->status == 200) {
  $pqrs = $pqrs->total;
} else {
  $pqrs = 0;
}

?>
<nav class="main-header navbar navbar-expand navbar-white navbar-light text-sm bg-info">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="#" class="nav-link">Sistema de Gestión Integral de ADULTO MAYOR</a>
    </li>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <!-- Messages Dropdown Menu -->
    <li class="nav-item dropdown">
      <?php if ($_SESSION["rols"]->name_class == "ADMINISTRADOR" || $_SESSION["rols"]->name_class == "SUPERVISOR") { ?>
      <a class="nav-link" href="/setpqrs" title="Pqrs">
        <i class="far fa-comment"></i>
        <span class="badge badge-info navbar-badge"><?php echo $pqrs ?></span>
      </a>
      <?php } ?>
    </li>
    <!-- Notifications Dropdown Menu -->
    <li class="nav-item dropdown">
      <?php if ($_SESSION["rols"]->name_class == "ADMINISTRADOR" || $_SESSION["rols"]->name_class == "SUPERVISOR") { ?>
        <a class="nav-link" href="/movalerts">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge"><?php echo $alerts ?></span>
        </a>
      <?php } ?>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-widget="fullscreen" href="/" role="button">
        <i class="fas fa-expand-arrows-alt"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/logout" role="button">
        <i class="fas fa-sign-out-alt"></i>
      </a>
    </li>
  </ul>
</nav>