<?php

namespace Config;
// Create a new instance of our RouteCollection class.
$session = \Config\Services::session();
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
if(!isset($_SESSION['usuario'])){
    $routes->get('/login', 'SessionController::showLogIn');
    $routes->get('/signup', 'SessionController::showSignUp');
    $routes->post('login', 'SessionController::LogInProcess');
    $routes->post('signup', 'SessionController::SignUp');
}else{
    $routes->get('imc', 'ImcController::showFormIMC');
    $routes->post('imc', 'ImcController::mostrarResForm');
    $routes->get('imc-edit', 'ImcController::showFormEditar');
    $routes->post('imc-edit', 'ImcController::formEditResResult');
    $routes->get('nutrients-week', 'EdamamController::showNutrientesSemana');
    $routes->get('meal-plan', 'EdamamController::showMealPlanDiario');
    $routes->get('meal-plan/(:any)', 'EdamamController::showMealPlanDiario/$1');
    $routes->post('meal-plan', 'EdamamController::getMealPlanDiario');
    $routes->get('recipe-search', 'SearchRecipeController::showForm');
    $routes->get('recipes', 'SearchRecipeController::showRecipes');
    $routes->post('add', 'BookmarksController::addBookmark');
    $routes->get('eliminar-receta-fav/(:num)', 'BookmarksController::deleteBookmark/$1');
    $routes->get('favoritos', 'BookmarksController::showBookmarks');
    $routes->get('cambiar-comida/(:any)/(:any)', 'EdamamController::regenerarComidaEntera/$1/$2');
    $routes->get('cambiar-comida-especifica/(:any)/(:any)/(:any)/(:any)', 'EdamamController::regenerarComidaEspecifica/$1/$2/$3/$4');
    $routes->get('eliminar-receta/(:num)/(:any)', 'EdamamController::eliminarRecetaEspecifica/$1/$2');
    $routes->post('next-page', 'SearchRecipeController::mostrarPagina');
    $routes->get('logout', 'SessionController::logOut');
    $routes->get('account', 'UsuarioController::showAccount');
    $routes->get('account/deletePeso/(:num)', 'UsuarioController::deletePeso/$1');
    $routes->post('account', 'UsuarioController::addPeso');
    $routes->get('change-pass', 'UsuarioController::showFormChangePass');
    $routes->post('change-pass', 'UsuarioController::changePassword');
    $routes->get('change-user', 'UsuarioController::showFormChangeUsername');
    $routes->get('delete-account', 'UsuarioController::showFormDeleteAccount');
    $routes->post('delete-account', 'UsuarioController::deleteAccount');
    $routes->post('change-user', 'UsuarioController::changeUsername');
}
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
