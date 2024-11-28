<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Tech Market</title>
    <link href="<?php echo ADMIN_URL; ?>css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.dataTables.min.css">k
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<style>
    #sidenavAccordion{
        background-color: #001d3b;;
    }

    .barra-navegacion{
        background-color: #001d3b;;
    }
</style>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand barra-navegacion">
        <a class="navbar-brand ps-3" href="<?php echo AYUDANTE_URL?>inicio2.php" style="color: white; text-align: center;">Tech Market/a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        <ul class="d-none d-md-inline-block navbar-nav ms-auto me-0 me-md-3 my-2 my-md-0">
            <li class="nav-item dropdown">
                <a style="color: white;" class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i><?php echo                                                                                                                                                               $_SESSION['user_name'] ?></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="<?php echo AYUDANTE_URL; ?>cambiar_password.php?id=<?php echo $_SESSION['user_id'] ?>">Cambiar Contraseña</a></li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li><a class="dropdown-item" href="<?php echo AYUDANTE_URL; ?>logout.php">Cerrar Sesion</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <a style="color: white;" class="nav-link" href="<?php echo AYUDANTE_URL?>productos">
                            <div class="sb-nav-link-icon"><i class="fas fa-box fa-2x"></i></div>
                            Productos
                        </a>

                        <a style="color: white;" class="nav-link" href="<?php echo AYUDANTE_URL?>compras">
                            <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart fa-2x"></i></div>
                            Ventas
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer" style="color: white;">
                    <div class="small" style="color: white;">Usuario: <?php echo $_SESSION['user_name'] ?></div>
                    Tech Market
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">