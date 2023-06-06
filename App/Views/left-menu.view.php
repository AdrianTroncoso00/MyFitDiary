<!DOCTYPE html>

<html lang="en">

    <head>
        <meta charset="utf-8" />
        <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
        <link rel="icon" type="image/png" href="../assets/img/favicon.ico">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <title>MyFitDiary</title>
        <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
        <!--     Fonts and icons     -->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />
        <!-- CSS Files -->


        <link href="assets/css/prueba.css" rel="stylesheet" />
        <link href="assets/css/light-bootstrap-dashboard.css?v=2.0.0 " rel="stylesheet" />
        <script src="https://kit.fontawesome.com/4799b9b69b.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

        <script src="assets/js/toggle.js"></script>
        <script src="assets/js/toTop.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
        <!--  Charts Plugin -->
        <script src="assets/js/chartist.min.js"></script>






    </head>

    <body>
        <div class="wrapper">
            <div class="sidebar" data-image="">
                <!--
            Tip 1: You can change the color of the sidebar using: data-color="purple | blue | green | orange | red"
    
            Tip 2: you can also add an image using data-image tag
                -->
                <div class="sidebar-wrapper">
                    <div class="logo">
                        <a href="" class="simple-text">
                            MyFitDiary
                        </a>
                    </div>
                    <ul class="nav">
                        <li class="nav-item active">
                            <a class="nav-link" href="dashboard.html">
                                <i class="fa-solid fa-user"></i>
                                <p><?php echo isset($_SESSION['usuario']['username']) ? $_SESSION['usuario']['username'] : 'usuario' ?></p>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link" href="/meal-plan">
                                <i class="fa-solid fa-utensils"></i>
                                <p>Plan Alimenticio</p>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link" href="/nutrientes-semana">
                                <i class="fa-brands fa-nutritionix"></i>
                                <p>Nutrientes Semanales</p>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link" href="/recipe-search">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <p>Buscador de Recetas</p>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link" href="/favoritos">
                                <i class="fa-solid fa-star"></i>
                                <p>Favoritos</p>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link" href="/imc">
                                <i class="fa-solid fa-gear"></i>
                                <p>Configuracion dieta</p>
                            </a>
                        </li>

                        <li class="nav-item active active-pro">
                            <a class="nav-link active" href="upgrade.html">
                                <i class="fa-sharp fa-solid fa-rocket"></i>
                                <p>Upgrade to PRO</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="main-panel">
                <!-- Navbar -->
                <nav class="navbar navbar-expand-lg " color-on-scroll="500">
                    <div class="container-fluid d-flex flex-row justify-content-end">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Account
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="/account">Show Account</a>
                                <a class="dropdown-item" href="/change-user">Change User</a>
                                <a class="dropdown-item" href="/change-pass">Change Pass</a>
                                <a class="dropdown-item text-danger" data-toggle="modal" data-target="#deleteAccountModal">Delete Account</a>
                            </div>
                            <div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Delete Account</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Esta seguro que quiere eliminar su cuenta permanentemente? Esta accion no se puede revocar.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <a type="button" href="/delete-account/<?php echo $_SESSION['usuario']['id']?>">Delete Account</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <a class="nav-link" href="/logout">
                            <span class="no-icon">Log out</span>
                        </a>
                    </div>
                </nav>

                <div class="content">
                    <div class="container-fluid">
