<?php

namespace App\Controllers;

use App\Controllers\EdamamController;

class SearchRecipeController extends \App\Core\BaseController {

    protected $firstPage = '';
    protected $nextPage = '';
    protected $previusPage = '';

    const API_EDAMAM_BUSCADOR_ID = '9cb2882a';
    const API_EDAMAM_BUSCADOR_KEY = '6caa45757c49df89e96822d45d4a517d';
    const URL_CONSULTA_BUSCADOR = 'https://api.edamam.com/api/recipes/v2?type=public&app_id=' . self::API_EDAMAM_BUSCADOR_ID . '&app_key=' . self::API_EDAMAM_BUSCADOR_KEY;
    const URL_CONSULTA_FIELD = '&field=label&field=image&field=url&field=yield&field=healthLabels&field=ingredientLines&field=calories&field=totalWeight&field=totalTime&field=mealType&field=totalNutrients';
    const MAX_RECETAS = 10;

    function showForm() {
        $modeloTipoComida = new \App\Models\TipoComidaModel();
        $modeloTipoCocina = new \App\Models\TipoCocinaModel();
        $modeloAlergenos = new \App\Models\AlergenosModel();
        $modeloDieta = new \App\Models\DietasModel();
        $modeloNombreComida = new \App\Models\NombreComidasModel();
        $data['nombreComidas'] = $modeloNombreComida->getAllNombresComidas();
        $data['tipoComida'] = $modeloTipoComida->getAll();
        $tipoCocina = $modeloTipoCocina->getAll();
        $data['tipoCocina'] = array_chunk($tipoCocina, 6);
        $dietas = $modeloDieta->getAllDietas();
        $data['dietas'] = array_chunk($dietas, 6);
        $alergenos = $modeloAlergenos->getAll();
        $data['alergenos'] = array_chunk($alergenos, 8);
        return view('templates/left-menu.view.php') . view('recipe-search-filtros.view.php', $data) . view('templates/footer.view.php');
    }

    function mostrarPagina() {
        $_SESSION['previusPage']= $_SESSION['actual'];
        $_SESSION['actual']=$_POST['nextPage'];
        unset($_SESSION['nextPage']);
        $recetas = EdamamController::getRequestCurlArray($_POST['nextPage'], true);
        if (count($recetas) > 0) {
            $recetasBuenas = $this->getAllNecesary($recetas);
            $data['recetas'] = $recetasBuenas;
            $linkNextPage = isset($recetas['_links']['next']['href']) ? $recetas['_links']['next']['href'] : null;
            $_SESSION['nextPage']=$linkNextPage;
            return view('templates/left-menu.view.php') . view('recipe-search-results.view.php', $data) . view('templates/footer.view.php');
        }
    }

    function showRecipes() {
        $resultados = $this->generarStringBuscadorRecetas($_GET);
        $cadenaParametros = $resultados['result'];
        $_SESSION['firstPage'] = $cadenaParametros;
        $errores = $resultados['errores'];
        if (count($errores) == 0) {
            $recetas = EdamamController::getRequestCurlArray($cadenaParametros, true);
            count($recetas['hits'])>0 ? $_SESSION['firstPage'] = $cadenaParametros : $_SESSION['firstPage']=null;
            $recetasBuenas = $this->getAllNecesary($recetas);
            $linkNextPage = isset($recetas['_links']['next']['href']) ? $recetas['_links']['next']['href'] : null;
            $_SESSION['nextPage']=$linkNextPage;
            $_SESSION['actual']=$cadenaParametros;
            $data['nextPage'] = $linkNextPage;
            $data['recetas'] = $recetasBuenas;
            return view('templates/left-menu.view.php') . view('recipe-search-results.view.php', $data) . view('templates/footer.view.php');
        } else {
            $data['errores'] = $errores;
            return view('templates/left-menu.view.php') . view('recipe-search-filtros.view.php', $data) . view('templates/footer.view.php');
        }
    }

    function generarStringBuscadorRecetas(array $params): array {
        $result = [];
        $errores = [];

        if (is_array($params['ingredients']) && count($params['ingredients']) == 1 && !empty($params['ingredients'][0])) {
            foreach ($params['ingredients'] as $ingrediente) {
                if (!preg_match('/[a-zA-Z ]{1, }/', $ingrediente)) {
                    $errores['ingredients'] = 'You can only enter letters and a minimum of 1 character.';
                } else {
                    $result['ingredients'] = 'q=' . str_replace(' ', '%20', implode('&q=', $params['ingredients']));
                }
            }
        }
        if (is_array($params['excluded']) && count($params['excluded']) == 1 && !empty($params['excluded'][0])) {
            foreach ($params['excluded'] as $excluded) {
                if (!preg_match('/[a-zA-Z ]{1, }/', $excluded)) {
                    $errores['excluded'] = 'You can only enter letters and a minimum of 1 character.';
                } else {
                    $result['excluded'] = 'excluded=' . str_replace(' ', '%20', implode('&excluded=', $params['excluded']));
                }
            }
        }
        if (!empty($params['minCalories']) && !empty($params['maxCalories'])) {
            if ((is_numeric($params['minCalories']) && $params['minCalories'] > 0) && (is_numeric($params['maxCalories']) && $params['maxCalories'] > 0)) {
                $result['calories'] = 'calories=' . $params['minCalories'] . '-' . $params['maxCalories'];
            } else {
                $errores['calories'] = 'The calories must be a number greater than 0.';
            }
        } else {
            if (!empty($params['minCalories'])) {
                if (is_numeric($params['minCalories']) && $params['minCalories'] > 0) {
                    $result['calories'] = 'calories=' . $params['minCalories'] . '%2B';
                } else {
                    $errores['calories'] = 'The calories must be a number greater than 0.';
                }
            }
            if (!empty($params['maxCalories'])) {
                if (is_numeric($params['maxCalories']) && $params['maxCalories'] > 0) {
                    $result['calories'] = 'calories=' . $params['maxCalories'];
                } else {
                    $errores['calories'] = 'The calories must be a number greater than 0.';
                }
            }
        }
        if (!empty($params['timeMin']) && !empty($params['timeMax'])) {
            if ((is_numeric($params['timeMin']) && $params['timeMin'] > 0) && (is_numeric($params['timeMax']) && $params['timeMax'] > 0)) {
                $result['time'] = 'time=' . $params['timeMin'] . '-' . $params['timeMax'];
            } else {
                $errores['time'] = 'The time must be a number greater than 0.';
            }
        } else {
            if (!empty($params['timeMin'])) {
                if (is_numeric($params['timeMin']) && $params['timeMin'] > 0) {
                    $result['time'] = 'time=' . $params['timeMin'] . '%2B';
                } else {
                    $errores['time'] = 'The time must be a number greater than 0.';
                }
            }
            if (!empty($params['timeMax'])) {
                if (is_numeric($params['timeMax']) && $params['timeMax'] > 0) {
                    $result['time'] = 'time=' . $params['timeMax'];
                } else {
                    $errores['time'] = 'The time must be a number greater than 0.';
                }
            }
        }
        if (!empty($params['mealType'])) {
            $result['mealType'] = 'mealType=' . str_replace(' ', '%20', implode('&mealType=', $params['mealType']));
        }
        if (!empty($params['dishType'])) {
            $result['dishType'] = 'dishType=' . str_replace(' ', '%20', implode('&dishType=', $params['dishType']));
        }
        if (!empty($params['dietLabels'])) {
            $result['diet'] = 'diet=' . str_replace(' ', '%20', implode('&diet=', $params['dietLabels']));
        }
        if (!empty($params['healthLabels'])) {
            $result['health'] = 'health=' . str_replace(' ', '%20', implode('&health=', $params['healthLabels']));
        }
        if (!empty($params['cuisineType'])) {
            $result['cuisineType'] = 'cuisineType=' . str_replace(' ', '%20', implode('&cuisineType=', $params['cuisineType']));
        }


        $result['cadenaTotal'] = '';
        foreach ($result as $value) {
            $result['cadenaTotal'] .= '&' . $value;
        }
        $cadenaParametros = trim($result['cadenaTotal'], '&');
        $cadenaTotal = self::URL_CONSULTA_BUSCADOR . '&' . $cadenaParametros;
        return ([
            'result' => $cadenaTotal,
            'errores' => $errores
        ]);
    }

    function getAllNecesary(array $recetas): array {
        $array = [];
        foreach ($recetas['hits'] as $key => $value) {
            $array[$key]['label'] = $value['recipe']['label'];
            $array[$key]['image'] = $value['recipe']['image'];
            $array[$key]['url'] = $value['recipe']['url'];
            $array[$key]['yield'] = $value['recipe']['yield'];
            $array[$key]['dietLabels'] = $value['recipe']['dietLabels'];
            $array[$key]['ingredientLines'] = $value['recipe']['ingredientLines'];
            $array[$key]['calories'] = round($value['recipe']['calories'], 0);
            $array[$key]['totalTime'] = round($value['recipe']['totalTime'], 0);
            $array[$key]['cuisineType'] = $value['recipe']['cuisineType'];
            $array[$key]['position'] = $key;
        }
        return $array;
    }

}
