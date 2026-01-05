<!-- Sidebar menu-->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
    <div class="app-sidebar__user"><img class="app-sidebar__user-avatar" src="<?= media(); ?>/images/avatar.png"
            alt="User Image">
        <div>
            <p class="app-sidebar__user-name" style="text-transform: capitalize; font-size: 10px;">
                <?= $_SESSION['userData']['nombre_usuario']; ?>
            </p>
            <p class="app-sidebar__user-designation" style="text-transform: capitalize; font-size: 10px;">
                <?= $_SESSION['userData']['nombre_rol']; ?>
            </p>
        </div>
    </div>
    <ul class="app-menu">
        <?php if (!empty($_SESSION['permisos'][1]['r_permiso'])) { ?>
            <li>
                <a class="app-menu__item" href="<?= base_url(); ?>/dashboard">
                    <i class="app-menu__icon fa fa-dashboard"></i>
                    <span class="app-menu__label">Dashboard</span>
                </a>
            </li>
        <?php } ?>
        <?php if (!empty($_SESSION['permisos'][2]['r_permiso'])) { ?>
            <li class="treeview">
                <a class="app-menu__item" href="#" data-toggle="treeview">
                    <i class="app-menu__icon fa fa-id-card" aria-hidden="true"></i>
                    <span class="app-menu__label">Usuarios</span>
                    <i class="treeview-indicator fa fa-angle-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a class="treeview-item" href="<?= base_url(); ?>usuarios"><i class="icon fa fa-circle-o"></i>
                            Usuarios</a></li>
                    <li><a class="treeview-item" href="<?= base_url(); ?>roles"><i class="icon fa fa-circle-o"></i>
                            Roles</a></li>
                </ul>
            </li>
        <?php } ?>

        <!-- Aqui debo colocar los permisos de control electoral de todos los modulos -->
        <?php if (
            !empty($_SESSION['permisos'][4]['r_permiso']) || !empty($_SESSION['permisos'][5]['r_permiso'])
            || !empty($_SESSION['permisos'][6]['r_permiso']) || !empty($_SESSION['permisos'][7]['r_permiso'])
        ) { ?>
            <li class="treeview">
                <a class="app-menu__item" href="#" data-toggle="treeview">
                    <i class="app-menu__icon fa fa-check-square-o" aria-hidden="true"></i>
                    <span class="app-menu__label">Control Encuestas</span>
                    <i class="treeview-indicator fa fa-angle-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php if (!empty($_SESSION['permisos'][4]['r_permiso'])) { ?>
                        <li><a class="treeview-item" href="<?= base_url(); ?>terceros"><i class="icon fa fa-circle-o"></i>
                                Terceros</a></li>
                    <?php } ?>
                    <?php if (!empty($_SESSION['permisos'][5]['r_permiso'])) { ?>
                        <li><a class="treeview-item" href="<?= base_url(); ?>encuestas"><i class="icon fa fa-circle-o"></i>
                                Encuestas</a></li>
                    <?php } ?>
                    <?php if (!empty($_SESSION['permisos'][6]['r_permiso'])) { ?>
                        <li><a class="treeview-item" href="<?= base_url(); ?>registro"><i class="icon fa fa-circle-o"></i>
                                Registro</a></li>
                    <?php } ?>
                    <?php if (!empty($_SESSION['permisos'][7]['r_permiso'])) { ?>
                        <li><a class="treeview-item" href="<?= base_url(); ?>edicion"><i class="icon fa fa-circle-o"></i>
                                Edicion</a></li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
        <?php if (!empty($_SESSION['permisos'][7]['r_permiso']) || !empty($_SESSION['permisos'][8]['r_permiso'])) { ?>
            <li class="treeview">
                <a class="app-menu__item" href="#" data-toggle="treeview">
                    <i class="app-menu__icon fa fa-bar-chart" aria-hidden="true"></i>
                    <span class="app-menu__label">Informes/Gráficos</span>
                    <i class="treeview-indicator fa fa-angle-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php if (!empty($_SESSION['permisos'][7]['r_permiso'])) { ?>
                        <li><a class="treeview-item" href="<?= base_url(); ?>infencuestas"
                                style="padding-left: 40px;"><i class="icon fa fa-circle-o"></i> Informe Encuestas</a></li>
                    <?php } ?>
                    <?php if (!empty($_SESSION['permisos'][8]['r_permiso'])) { ?>
                        <li><a class="treeview-item" href="<?= base_url(); ?>grafencuestas"
                                style="padding-left: 40px;"><i class="icon fa fa-circle-o"></i> Gráficos de Encuestas</a></li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
        <li>
            <a class="app-menu__item" href="<?= base_url(); ?>/logout">
                <i class="app-menu__icon fa fa-sign-out" aria-hidden="true"></i>
                <span class="app-menu__label">Logout</span>
            </a>
        </li>
    </ul>
</aside>