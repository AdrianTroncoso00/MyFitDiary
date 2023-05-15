<?php

namespace App\Controllers;

class SearchRecipeController extends \App\Core\BaseController {
    
    const API_EDAMAM_BUSCADOR_ID = '9cb2882a';
    const API_EDAMAM_BUSCADOR_KEY = '6caa45757c49df89e96822d45d4a517d';

    const URL_CONSULTA_BUSCADOR = 'https://api.edamam.com/api/recipes/v2?type=public&app_id='.self::API_EDAMAM_BUSCADOR_ID.'&app_key='.self::API_EDAMAM_BUSCADOR_KEY;
    const URL_CONSULTA_FIELD = '&field=label&field=image&field=url&field=yield&field=healthLabels&field=ingredientLines&field=calories&field=totalWeight&field=totalTime&field=mealType&field=totalNutrients';
    
    const MAX_RECETAS = 10;
    
    function getRequestCurlArray(string $parametros) {
        $curl = curl_init();

        $options = array(
            CURLOPT_URL => self::URL_CONSULTA_BUSCADOR .'&'.$parametros,
            CURLOPT_RETURNTRANSFER => true, //Sin esta línea se haría un echo de la respuesta en vez de guardarse en una variable del tipo string
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true, //Permite que si hay redirecciones las resuelva
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        );
        
        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);

        //Si no hubo errores en la petición comprobamos que se devuelve el código 200 como status
        if (!curl_errno($curl)) {
            switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
                case 200:  # OK                   
                    $recetas = (json_decode($response, true));
                    return $recetas;
                    
                default:
                    echo 'Unexpected HTTP code: ', $http_code, "\n";
                    
            }
        }
        curl_close($curl);
    }
    
    function showForm(){
        $modeloTipoComida = new \App\Models\TipoComidaModel();
        $modeloTipoCocina = new \App\Models\TipoCocinaModel();
        $modeloDieta = new \App\Models\DietaDiaModel();
        $modeloAlergenos = new \App\Models\AlergenosModel();
        $data['tipoComida'] = $modeloTipoComida->getAll();
        $data['tipoCocina'] = $modeloTipoCocina->getAll();
        $data['dietas'] = $modeloDieta->getAllDietas();
        $data['alergenos'] = $modeloAlergenos->getAll();
        $this->view->showViews(array('left-menu.view.php', 'recipe-search-filtros.view.php'), $data);
        
    }
    
    function showRecipes(){
        $cadenaParametros = $this->generarStringBuscadorRecetas($_GET);
        $recetas = $this->getRequestCurlArray($cadenaParametros);
        $linkNextPage = $recetas['_links']['next']['href'];
        $recetasBuenas = $this->getAllNecesary($recetas);
        $data['recetas']= $recetasBuenas;
        $this->view->showViews(array('left-menu.view.php', 'recipe-search-results.view.php'), $data);
    }
    
    function generarStringBuscadorRecetas(array $params):string{
        $result=[];
        if(isset($params['dishType'])){
            $result['dishType'] = 'dishType=' .str_replace(' ','%20',implode('&dishType=', $params['dishType']));
        }
        if(isset($params['dietLabels'])){
            $result['diet'] = 'diet=' .str_replace(' ','%20',implode('&diet=', $params['dietLabels']));
        }
        if(isset($params['healthLabels'])){
            $result['health'] = 'health=' .str_replace(' ','%20',implode('&health=', $params['healthLabels']));
        }
        if(isset($params['cuisineType'])){
            $result['cuisineType'] = 'cuisineType=' .str_replace(' ','%20',implode('&cuisineType=', $params['cuisineType']));
        }
        $result['cadenaTotal']='';
        foreach ($result as $value) {
            $result['cadenaTotal'] .= '&'.$value;
        }
        return trim($result['cadenaTotal'], '&');
    }
    
    function getAllNecesary(array $recetas): array{
        $array = [];
        foreach ($recetas['hits'] as $key=>$value) {   
            $array[$key]['label'] =$value['recipe']['label'];
            $array[$key]['image'] =$value['recipe']['image'];
            $array[$key]['url'] =$value['recipe']['url'];
            $array[$key]['yield'] =$value['recipe']['yield'];
            $array[$key]['dietLabels'] =$value['recipe']['dietLabels'];
            $array[$key]['ingredientLines'] =$value['recipe']['ingredientLines'];
            $array[$key]['calories'] =round($value['recipe']['calories'], 2);
            $array[$key]['totalTime'] =round($value['recipe']['totalTime'], 0);
            $array[$key]['cuisineType'] =$value['recipe']['cuisineType'];
            $array[$key]['position'] =$key;
            
        }
        
        return $array;
    }
}
