<?php

namespace Config;

// Create a new instance of our RouteCollection class.
session_start();
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(true);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
$routes->get('/login', 'SessionController::showLogIn');
$routes->get('/signup', 'SessionController::showSignUp');
$routes->post('login', 'SessionController::LogInProcess');
$routes->post('signup', 'SessionController::SignUp');
$routes->get('imc', 'ImcController::showFormIMC');
$routes->post('imc', 'ImcController::mostrarResForm');
$routes->get('prueba', 'EdamamController::prueba');
$routes->get('meal-plan', 'EdamamController::getMealPlanDiario2');
$routes->post('meal-plan', 'EdamamController::getMealPlanDiario');
$routes->get('meal-prueba', 'EdamamController::getMealPlanSemanal');
$routes->get('recipe-search', 'SearchRecipeController::showForm');
$routes->get('recipes', 'SearchRecipeController::showRecipes');
$routes->post('add', 'SearchRecipeController::addBookmark');
$routes->get('cambiar-comida/(:any)/(:any)', 'EdamamController::regenerarComidaEntera/$1/$2');
$routes->get('cambiar-comida-especifica/(:any)/(:any)/(:any)/(:any)', 'EdamamController::regenerarComidaEspecifica/$1/$2/$3/$4');
$routes->get('eliminar-receta/(:num)', 'EdamamController::eliminarRecetaEspecifica/$1');
$routes->get('pruebaSemanal', 'EdamamController::getMealPlanSemanal');
$routes->post('next-page', 'SearchRecipeController::mostrarPaginaSiguiente');

$routes->get('prueba', 'SessionController::pruebaVista');
$routes->get('logout', 'SessionController::logOut');
$routes->get('account', 'UsuarioController::showAccount');
$routes->get('account/deletePeso/(:num)', 'UsuarioController::deletePeso/$1');
$routes->get('account/edit-peso/(:num)', 'UsuarioController::showEdit/$1');
$routes->post('account/edit-peso/(:num)', 'UsuarioController::changePeso/$1');
$routes->post('account', 'UsuarioController::addPeso');
$routes->get('user/settings', 'UsuarioController::showFormChangePass');
/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
