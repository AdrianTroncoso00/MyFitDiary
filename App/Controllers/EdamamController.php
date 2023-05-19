<?php

namespace App\Controllers;

class EdamamController extends \App\Core\BaseController {

    const API_EDAMAM_BUSCADOR_ID = '9cb2882a';
    const API_EDAMAM_BUSCADOR_KEY = '6caa45757c49df89e96822d45d4a517d';
    const URL_CONSULTA_BUSCADOR = 'https://api.edamam.com/api/recipes/v2?type=public&app_id=9cb2882a&app_key=6caa45757c49df89e96822d45d4a517d';
    const URL_CONSULTA_FIELD = '&field=label&field=image&field=url&field=yield&field=healthLabels&field=ingredients&field=calories&field=totalWeight&field=totalTime&field=mealType&field=cuisineType&field=totalNutrients';
    const PORCENTAJE_COMIDA1 = 60;
    const PORCENTAJE_COMIDA2 = 40;
    
    const COMIDAS =['Breakfast', 'Snack', 'Lunch', 'Merienda','Dia final', 'Dinner'];
    
    function generarQuery(string $dieta, string $mealType):string{
        $query =self::URL_CONSULTA_BUSCADOR.'&diet='.$dieta.'&mealType='.$mealType.'&calories='.$caloriasMinimas.'-'.$caloriasMaximas.'&random=true&yield=1'.self::URL_CONSULTA_FIELD;
        
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
    
    function transformarArray(array $mealplan):array{
        $array= [];
        foreach ($mealplan as $key=> $comida) {
            $array[substr($comida['nombre_comida'], 0, -1)][$key]= ([
                'id_receta'=> $comida['id_comida'],
                'url'=> $comida['url'],
                'label' => $comida['label'],
                'calorias'=>$comida['calorias'],
                'image'=> $comida['image'],
                'totalTime' => $comida['totaltime'],
                'cuisineType'=> $comida['cuisinetype'],
                'ingredients'=> json_decode($comida['ingredients'],true),
                'nutrientes' => json_decode($comida['nutrientes'], true),
                'nombre_comida'=> $comida['nombre_comida'],
                'calorias_comida'=> $this->getCaloriasComida(substr($comida['nombre_comida'], 0, -1))
            ]);
            
        }
        return $array;
    }
    function transformarArraySinRecipe(array $mealplan):array{
        $array= [];
        foreach ($mealplan as $key=> $comida) {
            $array[$comida['recipe']['nombre_comida']][$key]= ([
                'url'=> $comida['recipe']['url'],
                'label' => $comida['recipe']['label'],
                'calorias'=>$comida['recipe']['calorias'],
                'image'=> $comida['recipe']['image'],
                'totalTime' => $comida['recipe']['totaltime'],
                'cuisineType'=> $comida['recipe']['cuisinetype'],
                'ingredients'=> $comida['recipe']['ingredients'],
                'nutrientes' => $comida['recipe']['nutrientes'],
                
            ]);
            
        }
        return $array;
    }
    
    function getMealPlanDiario() {
        $dia = date("j-n-Y");
        $modelo = new \App\Models\ComidasModel();
        $mealPlanDia= $modelo->getMealPlanDiaria($_SESSION['usuario']['id'], $dia);
        if(!is_null($mealPlanDia)){
            $data['mealPlan'] = $this->transformarArray($mealPlanDia);
            var_dump($_SESSION['usuario']);
            $nutrientesTotales= $this->getAllNutrientes($data['mealPlan']);
            $data['nutrientesTotales'] = $nutrientesTotales;
            
        }else{
            $caloriasAndMealPlan = $this->getCaloriasAndMealPlan($_SESSION['usuario']['num_comidas'], $_SESSION['usuario']['nombre_dieta']);
            $mealPlan = $caloriasAndMealPlan['mealPlan'];
            var_dump($mealPlan);
            $mealPlan2 = $this->transformarArray($mealPlan);
            $data['mealPlan'] = $mealPlan2;
            $nutrientesTotales = $this->getAllNutrientes($mealPlan);
            
        }
        $data['nutrientesTotales'] = $nutrientesTotales;
        $data['etiquetas'] = ['Proteinas', 'Grasas', 'Carbohidratos'];
        $data['valores_etiquetas'] = [round($nutrientesTotales['Protein']['cantidadTotal'],0), round($nutrientesTotales['Fat']['cantidadTotal'], 0), round($nutrientesTotales['Carbs']['cantidadTotal'], 0)];
        $data['chart_colors'] = [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 205, 86)'];
        $this->view->showViews(array('left-menu.view.php', 'meal-plan.view.php'), $data);
    }
    
    function regenerarComidaEntera(string $mealType){
        $fecha = date("j-n-Y");
        $modeloComida = new \App\Models\ComidasModel();
        $calorias = $this->getCaloriasComida($mealType);
        if($modeloComida->deleteComida($_SESSION['usuario']['id'],$fecha,$mealType)){
            $comida = $this->getComida($_SESSION['usuario']['nombre_dieta'], $mealType, $calorias);
            var_dump($comida);
            return redirect()->to('/meal-plan');
            
        }else{
            var_dump('hola');
            $data['error_regenerar']='Ha ocurrido un error al regenerar la comida';
            $this->view->showViews(array('left-menu.view.php', 'meal-plan.view.php'), $data);
        }
    }
    
    function regenerarComidaEspecifica(int $id_receta ,string $mealType){
        $fecha = date("j-n-Y");
        $modeloComida = new \App\Models\ComidasModel();
        $caloriasComidaTotal = $this->getCaloriasComida(substr($mealType,0,-1));
        $calorias = (substr($mealType, 0, -1)==1) ? ($caloriasComidaTotal * (self::PORCENTAJE_COMIDA1 / 100)) : ($caloriasComidaTotal * (self::PORCENTAJE_COMIDA2 / 100));
        $receta = $this->getComida($_SESSION['usuario']['nombre_dieta'], $mealType, $calorias);
        if($modeloComida->modificarComida($id_receta, $receta, $mealType)){
            return redirect()->to('/meal-plan');
        }else{
            var_dump('hola');
            $data['error_regenerar']='Ha ocurrido un error al regenerar la comida';
            $this->view->showViews(array('left-menu.view.php', 'meal-plan.view.php'), $data);
        }
    }

    function getComida(string $dieta, string $mealType, float $calorias) {
        $modelo = new \App\Models\ComidasModel();
        $dia = date("j-n-Y");
        if ($calorias <= 450) {
            $recetas = $this->getRequestCurlArray($dieta, $mealType, ($calorias-75), ($calorias+75));
            $recetaProcesada = $this->modifyReceta($recetas, $calorias, $mealType);
            if($modelo->addComida($_SESSION['usuario']['id'], $recetaProcesada[$mealType], ($mealType.'1'),$dia)){
                return $recetaProcesada;
            }
        } else {
            $caloriasReceta1 = $calorias * (self::PORCENTAJE_COMIDA1 / 100);
            $recetas = $this->getRequestCurlArray($dieta, $mealType, ($caloriasReceta1-75), ($caloriasReceta1+75));
            $receta = $this->modifyReceta($recetas, $caloriasReceta1,$mealType);
            $caloriasReceta2 = $calorias-$caloriasReceta1;
            $recetas2 = $this->getRequestCurlArray($dieta, $mealType, ($caloriasReceta2-75), ($caloriasReceta2+75));
            $receta2 = $this->modifyReceta($recetas2, ($calorias - $caloriasReceta1), $mealType);
            if($modelo->addComida($_SESSION['usuario']['id'], $receta[$mealType], ($mealType.'1'),$dia) && $modelo->addComida($_SESSION['usuario']['id'], $receta2[$mealType],($mealType.'2'),$dia)){
                return [
                    0=>$receta,
                    1=>$receta2
                ];
            }
        }
    }

    function modifyReceta(array $receta, float $calorias,string $mealtype): array {
        $nuevaReceta=[];
        $caloriasRacion = round(($receta['recipe']['calories'] / $receta['recipe']['yield']), 0);
        $yield2 = round(($calorias / $caloriasRacion), 0);
        $nuevaReceta[$mealtype]['url'] = $receta['recipe']['url'];
        $nuevaReceta[$mealtype]['image'] = $receta['recipe']['image'];
        $nuevaReceta[$mealtype]['label'] = $receta['recipe']['label'];
        $nuevaReceta[$mealtype]['totalTime'] = $receta['recipe']['totalTime'];
        $nuevaReceta[$mealtype]['cuisineType'] = $receta['recipe']['cuisineType'];
        $nuevaReceta[$mealtype]['calorias'] = round(($caloriasRacion * $yield2), 0);
        if ($receta['recipe']['yield'] !== $yield2) {
            foreach ($receta['recipe']['totalNutrients'] as $key => $nutriente) {
                $nutriente['quantity'] = round(($nutriente['quantity'] / $receta['recipe']['yield']) * $yield2, 0);
                $nuevaReceta[$mealtype]['nutrientes'][$key] = $nutriente;
            }
            foreach ($receta['recipe']['ingredients'] as $key => $ingrediente) {
                $nuevaReceta[$mealtype]['ingredientes'][$key]['quantity'] = round(($ingrediente['quantity'] / $receta['recipe']['yield']) * $yield2, 2);
                $nuevaReceta[$mealtype]['ingredientes'][$key]['measure'] = $ingrediente['measure'];
                $nuevaReceta[$mealtype]['ingredientes'][$key]['food'] = $ingrediente['food'];
                $nuevaReceta[$mealtype]['ingredientes'][$key]['image'] = $ingrediente['image'];
                $nuevaReceta[$mealtype]['ingredientes'][$key]['stringIngrediente'] = is_null($ingrediente['measure']) ? $ingrediente['food'] : $ingrediente['quantity'] . ' ' . $ingrediente['measure'] . ' ' . $ingrediente['food'];
            }
        }
        return $nuevaReceta;
    }
    
    function getCaloriasComida(string $comida):int{
        return $_SESSION['usuario']['calorias_objetivo']* ($_SESSION['usuario']['porcent_'. strtolower($comida)]/100);
    }
    
    function getCaloriasAndMealPlan(int $numComidas, string $dieta): array {
        $calorias['desayuno'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_breakfast'] / 100);
        $calorias['brunch'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_snack'] / 100);
        $calorias['comida'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_lunch'] / 100);
        $calorias['merienda'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_merienda'] / 100);
        $calorias['cena'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_dinner'] / 100);
        var_dump($calorias);
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
            foreach ($comida as $nutriente) {
                array_push($nutrientesTotales, $nutriente['nutrientes']); 
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
}
