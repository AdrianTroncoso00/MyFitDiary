<?php

namespace App\Controllers;

class EdamamController extends \App\Core\BaseController {

    const API_EDAMAM_BUSCADOR_ID = '9cb2882a';
    const API_EDAMAM_BUSCADOR_KEY = '6caa45757c49df89e96822d45d4a517d';

    const URL_CONSULTA_BUSCADOR = 'https://api.edamam.com/api/recipes/v2?type=public&app_id=9cb2882a&app_key=6caa45757c49df89e96822d45d4a517d';
    const URL_CONSULTA_FIELD = '&field=label&field=image&field=url&field=yield&field=healthLabels&field=ingredientLines&field=calories&field=totalWeight&field=totalTime&field=mealType&field=totalNutrients';
   
    const PORCENAJE_COMIDA1 = 60;
    const PORCENAJE_COMIDA2 = 40;

    function pruebaView(){
        $data['etiquetas'] = ['Proteinas', 'Grasas', 'Carbohidratos'];
        $data['valores_etiquetas'] = [200, 100, 50];
        $data['chart_colors'] = [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 205, 86)'];
        $this->view->showViews(array('left-menu.view.php', 'meal-plan.view.php'), $data);
    }
    
    function getRequestCurlArray(string $dieta, string $mealType) {
        $curl = curl_init();

        $options = array(
            CURLOPT_URL => self::URL_CONSULTA_BUSCADOR . "&diet=$dieta&mealType=$mealType&random=true&yield=1". self::URL_CONSULTA_FIELD,
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
                    return $recetas['hits'][rand(0, count($recetas['hits'])-1)];
                    
                default:
                    echo 'Unexpected HTTP code: ', $http_code, "\n";
                    
            }
        }
        curl_close($curl);
    }

    function getMealPlanDiario() {
        var_dump($_SESSION['usuario']);
        $modelo = new \App\Models\DietaDiaModel();
        $caloriasAndMealPlan = $this->getCaloriasAndMealPlan($_SESSION['usuario']['num_comidas'], $_SESSION['usuario']['nombre_dieta']);
        $mealPlan = $caloriasAndMealPlan['mealPlan'];
        $data['mealPlan'] = $mealPlan;
        $nutrientesTotales = $this->getAllNutrientes($mealPlan);
//        if($modelo->addDietaDia($_SESSION['usuario']['id'], $mealPlan)){
//            var_dump('hola');
//        }
        $data['nutrientesTotales'] = $nutrientesTotales;
        $data['etiquetas'] = ['Proteinas', 'Grasas', 'Carbohidratos'];
        $data['valores_etiquetas'] = [round($nutrientesTotales['Protein']['cantidadTotal'], 2), round($nutrientesTotales['Fat']['cantidadTotal'],2), round($nutrientesTotales['Carbs']['cantidadTotal'], 2)];
        $data['chart_colors'] = [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 205, 86)'];
        $this->view->showViews(array('left-menu.view.php', 'meal-plan.view.php'), $data);
    }
    
    function createArrayInsertBD(array $array){
        $datos['id_usuario']= $_SESSION['usuario']['id'];
        foreach ($array as $nombreComida => $infoComida) {
            $datos[$nombreComida];
        }
    }

    function getComida(string $dieta, string $mealType, float $calorias) {
        if ($calorias <= 450) {
            $recetas = $this->getRequestCurlArray($dieta, $mealType);
            $recetaSinProcesar = $recetas;
            $recetaProcesada = $this->modifyCaloriesReceta($recetaSinProcesar, $calorias, $dieta);
            return $recetaProcesada;
        } else {
            $caloriasReceta1 = $calorias * (self::PORCENAJE_COMIDA1 / 100);
            $recetas = $this->getRequestCurlArray($dieta, $mealType);  
            $recetaSinProcesar=$recetas;
            $receta = $this->modifyCaloriesReceta($recetaSinProcesar, $caloriasReceta1, $dieta);
            $recetas2= $this->getRequestCurlArray($dieta, $mealType);
            $recetaSinProcesar2=$recetas2;
            $receta2 = $this->modifyCaloriesReceta($recetaSinProcesar2, ($calorias - $caloriasReceta1), $dieta);
            return [
                0 => $receta,
                1 => $receta2
            ];
        }
    }

    function modifyCaloriesReceta(array $receta, float $calorias, string $dieta): array {
        $receta['recipe']['caloriasComida']=$calorias;
        $caloriasRacion = $receta['recipe']['calories'] / $receta['recipe']['yield'];
        $receta['recipe']['caloriasRacion'] = round($caloriasRacion,2);
        $receta['recipe']['yield2'] = round(($calorias / $caloriasRacion), 0);
        $receta['recipe']['calories'] = round(($caloriasRacion * $receta['recipe']['yield2']), 2);
//        if($receta['recipe']['calories']>($calorias+120)){
//            $receta2= $this->getRequestCurlArray($dieta, $receta['recipe']['mealType'][0]);
//            $recetaProcesada = $this->modifyCaloriesReceta($receta2, $calorias, $dieta);
//            return $recetaProcesada;
//        }
        
        if ($receta['recipe']['yield'] !== $receta['recipe']['yield2']) {
            foreach ($receta['recipe']['totalNutrients'] as $key => $nutriente) {
                $nutriente['quantity'] = round(($nutriente['quantity'] / $receta['recipe']['yield']) * $receta['recipe']['yield2'], 2);
                $receta['recipe']['totalNutrients'][$key] = $nutriente;
            }
        }
        unset($receta['_links']);
        return $receta;
    }

    function getCaloriasAndMealPlan(int $numComidas, string $dieta): array { 
        $calorias = [];
        $mealPlan = [];
        if ($numComidas==3) {
            $calorias['desayuno'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_desayuno'] / 100);
            $calorias['comida'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_comida'] / 100);
            $calorias['cena'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_cena'] / 100);
            $mealPlan['desayuno'] = $this->getComida($dieta, 'Breakfast', $calorias['desayuno']);
            $mealPlan['brunch'] = null;
            $mealPlan['comida'] = $this->getComida($dieta, 'Lunch', $calorias['comida']);
            $mealPlan['merienda'] = null;
            $mealPlan['cena'] = $this->getComida($dieta, 'Dinner', $calorias['cena']);
        }
        if ($numComidas==4) {
            $calorias['desayuno'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_desayuno'] / 100);
            $calorias['brunch'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_brunch'] / 100);
            $calorias['comida'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_comida'] / 100);
            $calorias['cena'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_cena'] / 100);
            $mealPlan['desayuno'] = $this->getComida($dieta, 'Breakfast', $calorias['desayuno']);
            $mealPlan['brunch'] = $this->getComida($dieta, 'Snack', $calorias['brunch']);
            $mealPlan['comida'] = $this->getComida($dieta, 'Lunch', $calorias['comida']);
            $mealPlan['merienda'] = null;
            $mealPlan['cena'] = $this->getComida($dieta, 'Dinner', $calorias['cena']);
        } 
        if($numComidas==5){
            $calorias['desayuno'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_desayuno'] / 100);
            $calorias['brunch'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_brunch'] / 100);
            $calorias['comida'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_comida'] / 100);
            $calorias['merienda'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_merienda'] / 100);
            $calorias['cena'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_cena'] / 100);
            $mealPlan['desayuno'] = $this->getComida($dieta, 'Breakfast', $calorias['desayuno']);
            $mealPlan['brunch'] = $this->getComida($dieta, 'Snack', $calorias['brunch']);
            $mealPlan['comida'] = $this->getComida($dieta, 'Lunch', $calorias['comida']);
            $mealPlan['merienda'] = $this->getComida($dieta, 'Teatime', $calorias['merienda']);
            $mealPlan['cena'] = $this->getComida($dieta, 'Dinner', $calorias['cena']);
        }
        return ([
            'calorias' => $calorias,
            'mealPlan' => $mealPlan
        ]);
    }
    
    function getAllNutrientes(array $mealplan):array{
        $nutrientesTotales=[];
        $nut=[];
        foreach ($mealplan as $comida) {
            foreach ($comida as $key=>$elemento) {
                var_dump($elemento);
                array_push($nutrientesTotales, $elemento['recipe']['totalNutrients']);   
            }
        }
        foreach ($nutrientesTotales as $key=> $nutriente) {
            foreach ($nutriente as $infoNutriente){
                $nut[$infoNutriente['label']]['cantidad'][$key]=$infoNutriente['quantity'];
                $nut[$infoNutriente['label']]['unidad']=$infoNutriente['unit'];
                $nut[$infoNutriente['label']]['cantidadTotal']=array_sum($nut[$infoNutriente['label']]['cantidad']);  
            }
        }
        return $nut;
    }
   
    
    function getValoresChart(array $nutrientes):array {
        $data['etiquetas'] = ['Proteinas', 'Grasas', 'Carbohidratos'];
        $data['valores_etiquetas'] = [$nutrientes['Protein']['cantidadTotal'], $nutrientes['Fat']['cantidadTotal'], $nutrientes['Carbs']['cantidadTotal']];
        $data['chart_colors'] = [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 205, 86)'];
        return $data;
    }

}
