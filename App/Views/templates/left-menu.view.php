<html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" type="image/png" href="assets/images/Myfitdiary.png"/>
        <title>
            MyFitDiary
        </title>
        <!--     Fonts and icons     -->
        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
        <!-- Nucleo Icons -->
        <script src="https://kit.fontawesome.com/4799b9b69b.js" crossorigin="anonymous"></script>

        <!-- CSS Files -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
        
        <link id="pagestyle" href="<?php echo base_url('assets/css/prueba.css') ?>" rel="stylesheet" />
        <link id="pagestyle" href="<?php echo base_url('assets/css/material-dashboard.css') ?>" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    </head>

    <body class="g-sidenav-show  bg-gray-200">
        <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark" id="sidenav-main">
            <div class="sidenav-header">
                <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
                <a class="navbar-brand m-0"" target="_blank">
                    <span class="ms-1 font-weight-bold text-white">MYFITDIARY</span>
                </a>
            </div>
            <hr class="horizontal light mt-0 mb-2">
            <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
                <ul class="navbar-nav">

                    <li class="nav-item">
                        <a class="nav-link text-white " href="/account">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <span class="nav-link-text ms-1 white"><?php echo isset($_SESSION['usuario']['username']) ? $_SESSION['usuario']['username'] : 'usuario' ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white " href="/meal-plan">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa-solid fa-utensils"></i>
                            </div>
                            <span class="nav-link-text ms-1 white">Meal Plan</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white " href="/nutrients-week">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa-brands fa-nutritionix"></i>
                            </div>
                            <span class="nav-link-text ms-1 white">Nutrients Week</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white " href="/recipe-search">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </div>
                            <span class="nav-link-text ms-1 white">Recipe Search</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white " href="/favoritos">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa-solid fa-star"></i>
                            </div>
                            <span class="nav-link-text ms-1 white">Bookmarks</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white " href="/imc-edit">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-gear"></i>
                            </div>
                            <span class="nav-link-text ms-1 white">Diet Configuration</span>
                        </a>
                    </li>

                    <li class="nav-item mt-3">
                        <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Account pages</h6>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white " href="/account">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <span class="nav-link-text ms-1 white">Profile</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white " href="/change-user">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                               <i class="fa-solid fa-user"></i>
                            </div>
                            <span class="nav-link-text ms-1 white">Change User</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white " href="/change-pass">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <span class="nav-link-text ms-1 white">Change Pass</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white " href="/delete-account">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa-solid fa-user-xmark"></i>
                            </div>
                            <span class="nav-link-text ms-1 white">Delete Account</span>
                        </a>
                    </li>

                </ul>
            </div>

        </aside>
        <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
            <!-- Navbar -->
            <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
                <div class="container-fluid py-1 px-3">

                    <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4 d-flex flex-row justify-content-end" id="navbar">

                        <ul class="navbar-nav  justify-content-end">

                            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                                <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                                    <div class="sidenav-toggler-inner">
                                        <i class="sidenav-toggler-line"></i>
                                        <i class="sidenav-toggler-line"></i>
                                        <i class="sidenav-toggler-line"></i>
                                    </div>
                                </a>
                            </li>

                            <li class="nav-item d-flex align-items-center">
                                <a href="/logout" class="nav-link text-body font-weight-bold ml-5">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                    <span class="d-sm-inline d-none">Log out</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <main  class="content mt-3">
                <section class="cntainer-fluid">