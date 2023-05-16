<?php

namespace App\Controllers;

class EdamamController extends \App\Core\BaseController {

    const API_EDAMAM_BUSCADOR_ID = '9cb2882a';
    const API_EDAMAM_BUSCADOR_KEY = '6caa45757c49df89e96822d45d4a517d';
    const URL_CONSULTA_BUSCADOR = 'https://api.edamam.com/api/recipes/v2?type=public&app_id=9cb2882a&app_key=6caa45757c49df89e96822d45d4a517d';
    const URL_CONSULTA_FIELD = '&field=label&field=image&field=url&field=yield&field=healthLabels&field=ingredients&field=calories&field=totalWeight&field=totalTime&field=mealType&field=totalNutrients';
    const PORCENAJE_COMIDA1 = 60;
    const PORCENAJE_COMIDA2 = 40;

    function pruebaView() {
        $data['etiquetas'] = ['Proteinas', 'Grasas', 'Carbohidratos'];
        $data['valores_etiquetas'] = [200, 100, 50];
        $data['chart_colors'] = [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 205, 86)'];
        $this->view->showViews(array('left-menu.view.php', 'meal-plan.view.php'), $data);
    }

    function getRequestCurlArray(string $dieta,string $mealType, int $caloriasMinimas, int $caloriasMaximas) {
        $curl = curl_init();

        $options = array(
            CURLOPT_URL => self::URL_CONSULTA_BUSCADOR.'&diet='.$dieta.'&mealType='.$mealType.'&calories='.$caloriasMinimas.'-'.$caloriasMaximas.'&random=true&yield=1'.self::URL_CONSULTA_FIELD,
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
                    return $recetas['hits'][rand(0, count($recetas['hits']) - 1)];

                default:
                    echo 'Unexpected HTTP code: ', $http_code, "\n";
            }
        }
        curl_close($curl);
    }

    function mejorarVista() {
        $data['etiquetas'] = ['Proteinas', 'Grasas', 'Carbohidratos'];
        $data['valores_etiquetas'] = [100, 50, 200];
        $data['chart_colors'] = [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 205, 86)'];
        $this->view->showViews(array('left-menu.view.php', 'meal-plan.view.php'), $data);
    }

    function getMealPlanDiario() {

        $modelo = new \App\Models\DietaDiaModel();
        $caloriasAndMealPlan = $this->getCaloriasAndMealPlan($_SESSION['usuario']['num_comidas'], $_SESSION['usuario']['nombre_dieta']);
        $mealPlan = $caloriasAndMealPlan['mealPlan'];
        $data['mealPlan'] = $mealPlan;
        $nutrientesTotales = $this->getAllNutrientes($mealPlan);
        $data['nutrientesTotales'] = $nutrientesTotales;
        $data['etiquetas'] = ['Proteinas', 'Grasas', 'Carbohidratos'];
        $data['valores_etiquetas'] = [round($nutrientesTotales['Protein']['cantidadTotal'], 2), round($nutrientesTotales['Fat']['cantidadTotal'], 2), round($nutrientesTotales['Carbs']['cantidadTotal'], 2)];
        $data['chart_colors'] = [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 205, 86)'];
        $this->view->showViews(array('left-menu.view.php', 'meal-plan.view.php'), $data);
    }

    function getComida(string $dieta, string $mealType, float $calorias) {
        if ($calorias <= 450) {
            $recetas = $this->getRequestCurlArray($dieta, $mealType, ($calorias-75), ($calorias+75));
            $recetaSinProcesar = $recetas;
            $recetaProcesada = $this->modifyReceta($recetaSinProcesar, $calorias);
            return $recetaProcesada;
        } else {
            $caloriasReceta1 = $calorias * (self::PORCENAJE_COMIDA1 / 100);
            $recetas = $this->getRequestCurlArray($dieta, $mealType, ($caloriasReceta1-75), ($caloriasReceta1+75));
            $recetaSinProcesar = $recetas;
            $receta = $this->modifyReceta($recetaSinProcesar, $caloriasReceta1);
            $caloriasReceta2 = $calorias-$caloriasReceta1;
            $recetas2 = $this->getRequestCurlArray($dieta, $mealType, ($caloriasReceta2-75), ($caloriasReceta2+75));
            $recetaSinProcesar2 = $recetas2;
            $receta2 = $this->modifyReceta($recetaSinProcesar2, ($calorias - $caloriasReceta1));
            return [
                0 => $receta,
                1 => $receta2
            ];
        }
    }

    function modifyReceta(array $receta, float $calorias): array {
        $receta['recipe']['caloriasComida'] = $calorias;
        $caloriasRacion = $receta['recipe']['calories'] / $receta['recipe']['yield'];
        $receta['recipe']['caloriasRacion'] = round($caloriasRacion, 2);
        $receta['recipe']['yield2'] = round(($calorias / $caloriasRacion), 0);
        $receta['recipe']['calories'] = round(($caloriasRacion * $receta['recipe']['yield2']), 2);
        if ($receta['recipe']['yield'] !== $receta['recipe']['yield2']) {
            foreach ($receta['recipe']['totalNutrients'] as $key => $nutriente) {
                $nutriente['quantity'] = round(($nutriente['quantity'] / $receta['recipe']['yield']) * $receta['recipe']['yield2'], 2);
                $receta['recipe']['totalNutrients'][$key] = $nutriente;
            }
            foreach ($receta['recipe']['ingredients'] as $key => $ingrediente) {
                $receta['recipe']['ingredientes'][$key]['quantity'] = round(($ingrediente['quantity'] / $receta['recipe']['yield']) * $receta['recipe']['yield2'], 2);
                $receta['recipe']['ingredientes'][$key]['measure'] = $ingrediente['measure'];
                $receta['recipe']['ingredientes'][$key]['food'] = $ingrediente['food'];
                $receta['recipe']['ingredientes'][$key]['image'] = $ingrediente['image'];
                $receta['recipe']['ingredientes'][$key]['stringIngrediente'] = is_null($receta['recipe']['ingredientes'][$key]['measure']) ? $ingrediente['food'] : $receta['recipe']['ingredientes'][$key]['quantity'] . ' ' . $receta['recipe']['ingredientes'][$key]['measure'] . ' ' . $receta['recipe']['ingredientes'][$key]['food'];
            }
        }
        unset($receta['_links']);
        return $receta;
    }

    function getCaloriasAndMealPlan(int $numComidas, string $dieta): array {
        $calorias['desayuno'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_desayuno'] / 100);
        $calorias['brunch'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_brunch'] / 100);
        $calorias['comida'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_comida'] / 100);
        $calorias['merienda'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_merienda'] / 100);
        $calorias['cena'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_cena'] / 100);
        
        $mealPlan = [];
        if ($numComidas == 3) {
            $mealPlan['desayuno'] = $this->getComida($dieta, 'Breakfast', $calorias['desayuno']);
            $mealPlan['comida'] = $this->getComida($dieta, 'Lunch', $calorias['comida']);
            $mealPlan['cena'] = $this->getComida($dieta, 'Dinner', $calorias['cena']);
        }
        if ($numComidas == 4) {
            $mealPlan['desayuno'] = $this->getComida($dieta, 'Breakfast', $calorias['desayuno']);
            $mealPlan['brunch'] = $this->getComida($dieta, 'Snack', $calorias['brunch']);
            $mealPlan['comida'] = $this->getComida($dieta, 'Lunch', $calorias['comida']);
            $mealPlan['cena'] = $this->getComida($dieta, 'Dinner', $calorias['cena']);
        }
        if ($numComidas == 5) {
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

    function getAllNutrientes(array $mealplan): array {
        $nutrientesTotales = [];
        $nut = [];
        foreach ($mealplan as $comida) {
            if (!is_null($comida)) {
                foreach ($comida as $key => $elemento) {
                    array_push($nutrientesTotales, $elemento['recipe']['totalNutrients']);
                }
            } else {
                unset($mealplan[$comida]);
            }
        }
        foreach ($nutrientesTotales as $key => $nutriente) {
            foreach ($nutriente as $infoNutriente) {
                $nut[$infoNutriente['label']]['cantidad'][$key] = $infoNutriente['quantity'];
                $nut[$infoNutriente['label']]['unidad'] = $infoNutriente['unit'];
                $nut[$infoNutriente['label']]['cantidadTotal'] = array_sum($nut[$infoNutriente['label']]['cantidad']);
            }
        }

        return $nut;
    }

    function getValoresChart(array $nutrientes): array {
        $data['etiquetas'] = ['Proteinas', 'Grasas', 'Carbohidratos'];
        $data['valores_etiquetas'] = [$nutrientes['Protein']['cantidadTotal'], $nutrientes['Fat']['cantidadTotal'], $nutrientes['Carbs']['cantidadTotal']];
        $data['chart_colors'] = [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 205, 86)'];
        return $data;
    }

}
