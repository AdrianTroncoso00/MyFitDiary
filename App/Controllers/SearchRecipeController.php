<?php

namespace App\Controllers;

class SearchRecipeController extends \App\Core\BaseController {
    
    const API_EDAMAM_BUSCADOR_ID = '9cb2882a';
    const API_EDAMAM_BUSCADOR_KEY = '6caa45757c49df89e96822d45d4a517d';

    const URL_CONSULTA_BUSCADOR = 'https://api.edamam.com/api/recipes/v2?type=public&app_id='.self::API_EDAMAM_BUSCADOR_ID.'&app_key='.self::API_EDAMAM_BUSCADOR_KEY;
    const URL_CONSULTA_FIELD = '&field=label&field=image&field=url&field=yield&field=healthLabels&field=ingredientLines&field=calories&field=totalWeight&field=totalTime&field=mealType&field=totalNutrients';
    
    const MAX_RECETAS = 10;
    
    function getRequestCurlArray(string $parametros='', string $urlCompleta='') {
        $curl = curl_init();
        $options = array(
            CURLOPT_URL => empty($urlCompleta) ? self::URL_CONSULTA_BUSCADOR .'&'.$parametros : $urlCompleta,
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
        $modeloAlergenos = new \App\Models\AlergenosModel();
        $modeloDieta = new \App\Models\DietasModel();
        $modeloNombreComida = new \App\Models\NombreComidasModel();
        $data['nombreComidas']= $modeloNombreComida->getAllNombresComidas();
        $data['tipoComida'] = $modeloTipoComida->getAll();
        $data['tipoCocina'] = $modeloTipoCocina->getAll();
        $data['dietas'] = $modeloDieta->getAllDietas();
        $data['alergenos'] = $modeloAlergenos->getAll();
        $this->view->showViews(array('left-menu.view.php', 'recipe-search-filtros.view.php'), $data);
        
    }
    
    function mostrarPaginaAnterior(){
        
    }
    
    function mostrarPaginaSiguiente(){
        $recetas= $this->getRequestCurlArray('', $_POST['nextPage']);
        if(count($recetas)>0){
            $recetasBuenas = $this->getAllNecesary($recetas);
            $data['recetas']= $recetasBuenas;
            $linkNextPage = isset($recetas['_links']['next']['href']) ? $recetas['_links']['next']['href'] : null;
            $data['nextPage']= $linkNextPage;
            $this->view->showViews(array('left-menu.view.php', 'recipe-search-results.view.php'), $data);   
        }
    }
    
    function addBookmark(){
        $modelo = new \App\Models\RecetasFavoritasModel();  
        if($modelo->addRecetaFavorita($_SESSION['usuario']['id'], $_POST)){
            return redirect()->to($_SERVER['HTTP_REFERER']);
        }else{
            $data['errorGuardar']='Ha ocurrido un error indeterminado al guardar, vuelve a intentarlo mas tarde';
            $this->view->showViews(array('left-menu.view.php', 'recipe-search-results.view.php'), $data);
        }
    }
    
    function showRecipes(){
        var_dump($_GET);
        $resultados  = $this->generarStringBuscadorRecetas($_GET);
        $cadenaParametros= $resultados['result'];
        $errores= $resultados['errores'];
        if(count($errores)==0){
            var_dump($cadenaParametros);
            $recetas = $this->getRequestCurlArray($cadenaParametros['cadenaTotal']);
            var_dump($recetas);
            $recetasBuenas = $this->getAllNecesary($recetas);
            $data['recetas']= $recetasBuenas;
            $linkNextPage = isset($recetas['_links']['next']['href']) ? $recetas['_links']['next']['href'] : null;
            $data['nextPage']= $linkNextPage;
            $this->view->showViews(array('left-menu.view.php', 'recipe-search-results.view.php'), $data);   
        }else{
            $data['errores']=$errores;
            $this->view->showViews(array('left-menu.view.php', 'recipe-search-filtros.view.php'), $data);   
            
        }
    }
    
    function generarStringBuscadorRecetas(array $params):array{
        $result=[];
        $errores=[];
        if(!empty($params)){
            if(is_array($params['ingredients']) && count($params['ingredients'])==1 && !empty($params['ingredients'][0])){
                $result['ingredients']= 'q=' .str_replace(' ','%20',implode('&q=', $params['ingredients']));
            }
            if(is_array($params['excluded']) && count($params['excluded'])==1 &&!empty($params['excluded'][0])){
                $result['excluded']= 'excluded=' .str_replace(' ','%20',implode('&excluded=', $params['excluded']));
            }
            if(!empty($params['minCalories']) && !empty($params['maxCalories'])) {
                if((is_numeric($params['minCalories']) && $params['minCalories']>0) && (is_numeric($params['maxCalories']) && $params['maxCalories']>0)){
                    $result['calories']='calories='. $params['minCalories']. '-'. $params['maxCalories'];
                }else{
                    $errores['calories']='Las calorias tienen que ser un numero mayor que 0';
                }

            }else{
                if(!empty($params['minCalories'])){
                    if(is_numeric($params['minCalories']) && $params['minCalories']>0){
                        $result['calories']='calories='. $params['minCalories'].'%2B';
                    }else{
                        $errores['calories']='Las calorias tienen que ser un numero mayor que 0';
                    }
                }
                if(!empty($params['maxCalories'])){
                    if(is_numeric($params['maxCalories']) && $params['maxCalories']>0){
                        $result['calories']='calories='. $params['maxCalories'];
                    }else{
                        $errores['calories']='Las calorias tienen que ser un numero mayor que 0';

                    }
                }

            }
            if(!empty($params['timeMin']) && !empty($params['timeMax'])) {
                if((is_numeric($params['timeMin']) && $params['timeMin']>0) && (is_numeric($params['timeMax']) && $params['timeMax']>0)){
                    $result['time']='time='. $params['timeMin']. '-'. $params['timeMax'];
                }else{
                    $errores['time']='El tiempo tienen que ser un numero mayor que 0';
                }

            }else{
                if(!empty($params['timeMin'])){
                    if(is_numeric($params['timeMin']) && $params['timeMin']>0){
                        $result['time']='time='. $params['timeMin'].'%2B';
                    }else{
                        $errores['time']='El tiempo tienen que ser un numero mayor que 0';
                    }
                }
                if(!empty($params['timeMax'])){
                    if(is_numeric($params['timeMax']) && $params['timeMax']>0){
                        $result['time']='time='. $params['timeMax'];
                    }else{
                        $errores['time']='El tiempo tienen que ser un numero mayor que 0';

                    }
                }

            }
            if(!empty($params['mealType'])){
                $result['mealType'] = 'mealType=' .str_replace(' ','%20',implode('&mealType=', $params['mealType']));
                
            }
            if(!empty($params['dishType'])){
                $result['dishType'] = 'dishType=' .str_replace(' ','%20',implode('&dishType=', $params['dishType']));
            }
            if(!empty($params['dietLabels'])){
                $result['diet'] = 'diet=' .str_replace(' ','%20',implode('&diet=', $params['dietLabels']));
            }
            if(!empty($params['healthLabels'])){
                $result['health'] = 'health=' .str_replace(' ','%20',implode('&health=', $params['healthLabels']));
            }
            if(!empty($params['cuisineType'])){
                $result['cuisineType'] = 'cuisineType=' .str_replace(' ','%20',implode('&cuisineType=', $params['cuisineType']));
            }
        }else{
            $errores['recipeSearch'] = 'Tiene que introducir minimo un parametro de busqueda';
        }
        
        $result['cadenaTotal']='';
        foreach ($result as $value) {
            $result['cadenaTotal'] .= '&'.$value;
        }
        $result['cadenaTotal']=trim($result['cadenaTotal'], '&');
        return ([
            'result'    =>$result,
            'errores'   =>$errores
        ]);
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
            $array[$key]['calories'] =round($value['recipe']['calories'], 0);
            $array[$key]['totalTime'] =round($value['recipe']['totalTime'], 0);
            $array[$key]['cuisineType'] =$value['recipe']['cuisineType'];
            $array[$key]['position'] =$key;
            
        }
        
        return $array;
    }
    
}
