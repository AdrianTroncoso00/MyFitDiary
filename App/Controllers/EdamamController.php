<?php

namespace App\Controllers;

class EdamamController extends \App\Core\BaseController {

    const API_EDAMAM_BUSCADOR_ID = '9cb2882a';
    const API_EDAMAM_BUSCADOR_KEY = '6caa45757c49df89e96822d45d4a517d';
    const URL_CONSULTA_BUSCADOR = 'https://api.edamam.com/api/recipes/v2?type=public&app_id=9cb2882a&app_key=6caa45757c49df89e96822d45d4a517d';
    const URL_CONSULTA_FIELD = '&field=label&field=image&field=url&field=yield&field=healthLabels&field=ingredients&field=calories&field=totalWeight&field=totalTime&field=mealType&field=cuisineType&field=totalNutrients';
    const PORCENTAJE_COMIDA1 = 50;
    const PORCENTAJE_COMIDA2 = 50;
    const COMIDAS = ['Breakfast', 'Snack', 'Lunch', 'Merienda', 'Dia final', 'Dinner'];

    function generarQuery(string $dieta, string $mealType): string{
        
        return self::URL_CONSULTA_BUSCADOR . '&diet=' . $dieta . '&mealType=' . $mealType. '&health=vegan&health=peanut-free&health=pescatarian&rand=true'. self::URL_CONSULTA_FIELD;
    }
    
    function prueba(){
        $randoms = range(0, 5);
        shuffle($randoms);
        var_dump($randoms);
    }
    
    function getMealPlanForm(){
        $date = date("j-n-Y");
    }
    
    function getRequestCurlArray(string $query){
        $curl = curl_init();

        $options = array(
            CURLOPT_URL => $query,
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
                    return $recetas['hits'];
                default:
                    echo 'Unexpected HTTP code: ', $http_code, "\n";
            }
        }
        curl_close($curl);
    }

    function transformarArray(array $mealplan): array {
        $array = [];
        foreach ($mealplan as $key => $comida) {
            $array[substr($comida['nombre_comida'], 0, -1)][$key] = ([
                'id_receta' => $comida['id_comida'],
                'url' => $comida['url'],
                'label' => $comida['label'],
                'calorias' => $comida['calorias'],
                'yield'     => $comida['yield'],
                'image' => $comida['image'],
                'totalTime' => $comida['totaltime'],
                'cuisineType' => $comida['cuisinetype'],
                'ingredientes' => json_decode($comida['ingredients'], true),
                'nutrientes' => json_decode($comida['nutrientes'], true),
                'nombre_comida' => $comida['nombre_comida'],
                'healthLabels'    =>json_decode($comida['healthlabels'],true),
                'calorias_comida' => $this->getCaloriasComida(substr($comida['nombre_comida'], 0, -1)) /2
            ]);
        }
        return $array;
    }
    
    function getMealPlanDiario2() {
        $dia = date("j-n-Y");
        var_dump($_SESSION);
        $modelo = new \App\Models\ComidasModel();
        $mealPlanDia = $modelo->getMealPlanDiaria($_SESSION['usuario']['id'], $dia);
        if (!is_null($mealPlanDia)) {
            $data['mealPlan'] = $this->transformarArray($mealPlanDia);
            $nutrientesTotales = $this->getAllNutrientes($mealPlanDia);
            $data['nutrientesTotales'] = $nutrientesTotales;
            $data['etiquetas'] = ['Proteinas', 'Grasas', 'Carbohidratos'];
            $data['valores_etiquetas'] = [round($nutrientesTotales['Protein']['cantidadTotal'], 0), round($nutrientesTotales['Fat']['cantidadTotal'], 0), round($nutrientesTotales['Carbs']['cantidadTotal'], 0)];
            $data['chart_colors'] = [
                'rgb(255, 99, 132)',
                'rgb(54, 162, 235)',
                'rgb(255, 205, 86)'];
            $data['fecha']= $dia;
            $data['input']['fecha']= date("d/m/Y", strtotime($dia));
            return view('left-menu.view.php'). view('meal-plan.view.php',$data);
        } else {
            return view('left-menu.view.php'). view('p.view.php');
//            $this->getCaloriasAndMealPlan($_SESSION['usuario']['num_comidas'], $_SESSION['usuario']['nombre_dieta']);
//            $mealPlan = $modelo->getMealPlanDiaria($_SESSION['usuario']['id'], $dia);
//            $data['mealPlan'] = $this->transformarArray($mealPlan);
//            $nutrientesTotales = $this->getAllNutrientes($mealPlan);
        }
    }
    function getMealPlanDiario() {
        $dia = date("j-n-Y", strtotime($_POST['fecha']));
        var_dump($dia);
        $modelo = new \App\Models\ComidasModel();
        $mealPlanDia = $modelo->getMealPlanDiaria($_SESSION['usuario']['id'], $dia);
        if (!is_null($mealPlanDia)) {
            $data['mealPlan'] = $this->transformarArray($mealPlanDia);
            $data['fecha']= $_POST['fecha'];
            $nutrientesTotales = $this->getAllNutrientes($mealPlanDia);
            $data['input']= filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        } else {
            $this->getCaloriasAndMealPlan($_SESSION['usuario']['num_comidas'], $_SESSION['usuario']['nombre_dieta'], $dia);
            $mealPlan = $modelo->getMealPlanDiaria($_SESSION['usuario']['id'], $dia);
            if(is_null($mealPlan)){
                var_dump('nulo');
            }
            $data['mealPlan'] = $this->transformarArray($mealPlan);
            $data['fecha']= $_POST['fecha'];
            $nutrientesTotales = $this->getAllNutrientes($mealPlan);
            $data['input']= filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        }
        $data['nutrientesTotales'] = $nutrientesTotales;
        $data['etiquetas'] = ['Proteinas', 'Grasas', 'Carbohidratos'];
        $data['valores_etiquetas'] = [round($nutrientesTotales['Protein']['cantidadTotal'], 0), round($nutrientesTotales['Fat']['cantidadTotal'], 0), round($nutrientesTotales['Carbs']['cantidadTotal'], 0)];
        $data['chart_colors'] = [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 205, 86)'];
        return view('left-menu.view.php'). view('meal-plan.view.php',$data);
    }

    function regenerarComidaEntera(string $mealType, string $date) {
        $fecha = date("j-n-Y", strtotime($date));
        $modeloComida = new \App\Models\ComidasModel();
        $calorias = $this->getCaloriasComida($mealType);
        $comidas = $this->getComida($_SESSION['usuario']['nombre_dieta'], $mealType, $calorias, $fecha);
        foreach ($comidas as $key=>$comida) {
            if (!$modeloComida->modificarComida($_SESSION['usuario']['id'], $fecha, ($mealType . ($key+1)), $comida)) {
                $_SESSION['usuario']['error'] = 'No se ha podido regenerar la comida';
                redirect()->to('meal-plan');
                unset($_SESSION['usuario']['error']);
            }
 
        }
        return redirect()->to('meal-plan');
    }

    function regenerarComidaEspecifica(int $id_receta, string $nombreComida, int $caloriasComida, string $date) {
        $dia = date("j-n-Y", strtotime($date));
        $modeloComida = new \App\Models\ComidasModel();
        $receta = $this->getComida($_SESSION['usuario']['nombre_dieta'], substr($nombreComida, 0, -1), $caloriasComida,$dia);
        if ($modeloComida->modificarRecetaEspecifica($id_receta, $receta[0])) {
            return redirect()->to('/meal-plan');
        } else {
            $_SESSION['usuario']['error'] = 'Ha ocurrido un error al regenerar la comida';
            redirect()->to('/meal-plan');
            unset($_SESSION['usuario']['error']);
        }
    }
    
    function eliminarRecetaEspecifica(int $id_comida){
        $modelo = new \App\Models\ComidasModel();
        if($modelo->deleteComidaEspecifica($id_comida)){
            return redirect()->to('/meal-plan');
        }else{
            $data['error']='Ha ocurrido un error al eliminar la comida';
            return view('left-menu.view.php'). view('meal-plan.view.php',$data);
        }
    }
    
    function getComida(string $dieta, string $mealType, int $calorias, string $date): ?array {
        $modelo = new \App\Models\ComidasModel();
        $dia = date("j-n-Y", strtotime($date));
        $query= $this->generarQuery($dieta, $mealType);
        $recetas = $this->getRequestCurlArray($query);
        if ($calorias <= 450) {
            $receta = $this->obtenerRecetasSinRepetir($recetas, $mealType, $calorias, 1, $dia);
            if ($modelo->existeComidaDia($dia, $_SESSION['usuario']['id'], $mealType)) {
                return $receta;
            }else{
                if ($modelo->addComida($_SESSION['usuario']['id'], $receta[0], ($mealType . '1'), $dia)) {
                    return $receta;
                } 
            } 
        } else {
            $recetasProcesadas = $this->obtenerRecetasSinRepetir($recetas, $mealType, $calorias, 2, $dia);
            if ($modelo->existeComidaDia($dia, $_SESSION['usuario']['id'], $mealType)) {
                return $recetasProcesadas;
            }else {
                if ($modelo->addComida($_SESSION['usuario']['id'], $recetasProcesadas[0], ($mealType . '1'), $dia) && $modelo->addComida($_SESSION['usuario']['id'], $recetasProcesadas[1], ($mealType . '2'), $dia)) {
                    return $recetasProcesadas;
                }
            }
        }
    }

    function obtenerRecetasSinRepetir(array $recetas, string $mealType, int $calorias, int $numComidas, string $fecha): ?array {
        $dia = date("j-n-Y", strtotime($fecha));
        $modelo = new \App\Models\ComidasModel();
        $recetasCopia = $recetas;
        $randoms = range(0, count($recetas)-1);
        $recetasSemanaComidas = $modelo->getComidasSemana($_SESSION['usuario']['id'], $dia);
        $nums =[];
        $recetasInput = [];
        $posiciones=[];
        if(!is_null($recetasSemanaComidas)){
            foreach ($recetasSemanaComidas as $recetaSemana) {
                foreach ($recetas as $key=> $receta) {
                    if($receta['recipe']['label']==$recetaSemana['label']){
                        unset($recetas[$key]);
                        array_push($nums, $key);
                    }
                }
            }
            if(count($recetas)>$numComidas){
                $posiciones = array_diff($randoms, $nums);
                shuffle($posiciones);  
            }else{
                $posiciones= range(0, count($recetasCopia)-1);
                shuffle($posiciones);
            }
        }else{
            $posiciones=$randoms;
            shuffle($posiciones);
        }
        $caloriasInput= $numComidas==1 ? $calorias : $calorias/2;
        if(count($recetas)>$numComidas){
            for($j=0; $j<$numComidas; $j++){
                $random= $posiciones[$j];
                $receta = $this->modifyReceta($recetas[$random], $caloriasInput, $mealType);
                array_push($recetasInput ,$receta);
            }
        }
        return $recetasInput;
    }

    function modifyReceta(array $receta, float $calorias, string $mealType): array {
        $nuevaReceta = [];
        $caloriasRacion = $receta['recipe']['calories']==0 ? 0 : round(($receta['recipe']['calories'] / $receta['recipe']['yield']), 0);
        $yield = $caloriasRacion==0 ? 1 : round(($calorias / $caloriasRacion), 0);
        $nuevaReceta['url'] = $receta['recipe']['url'];
        $nuevaReceta['image'] = $receta['recipe']['image'];
        $nuevaReceta['label'] = $receta['recipe']['label'];
        $nuevaReceta['totalTime'] = $receta['recipe']['totalTime'];
        $nuevaReceta['cuisineType'] = $receta['recipe']['cuisineType'];
        $nuevaReceta['calorias'] = round(($caloriasRacion * $yield), 0);
        $nuevaReceta['yield'] = $yield;
        $nuevaReceta['nombre_comida'] = $mealType;
        $nuevaReceta['healthLabels']= $receta['recipe']['healthLabels'];
        if ($receta['recipe']['yield'] !== $yield) {
            foreach ($receta['recipe']['totalNutrients'] as $key => $nutriente) {
                $nutriente['quantity'] = round(($nutriente['quantity'] / $receta['recipe']['yield']) * $yield, 0);
                $nuevaReceta['nutrientes'][$key] = $nutriente;
            }
            foreach ($receta['recipe']['ingredients'] as $key => $ingrediente) {
                $nuevaReceta['ingredientes'][$key]['quantity'] = round(($ingrediente['quantity'] / $receta['recipe']['yield']) * $yield, 2);
                $nuevaReceta['ingredientes'][$key]['measure'] = $ingrediente['measure'];
                $nuevaReceta['ingredientes'][$key]['food'] = $ingrediente['food'];
                $nuevaReceta['ingredientes'][$key]['image'] = $ingrediente['image'];
                $nuevaReceta['ingredientes'][$key]['stringIngrediente'] = is_null($ingrediente['measure']) ? $ingrediente['food'] : $ingrediente['quantity'] . ' ' . $ingrediente['measure'] . ' ' . $ingrediente['food'];
            }
        } else {
            $nuevaReceta['nutrientes'] = $receta['recipe']['totalNutrients'];
            foreach ($receta['recipe']['ingredients'] as $key=>$ingrediente) {
                $nuevaReceta['ingredientes'][$key]['quantity'] = $ingrediente['quantity'];
                $nuevaReceta['ingredientes'][$key]['measure'] = $ingrediente['measure'];
                $nuevaReceta['ingredientes'][$key]['food'] = $ingrediente['food'];
                $nuevaReceta['ingredientes'][$key]['image'] = $ingrediente['image'];
                $nuevaReceta['ingredientes'][$key]['stringIngrediente']= $ingrediente['text'];
            }
        }
        return $nuevaReceta;
    }

    function getCaloriasComida(string $comida): int {
        return $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_' . strtolower($comida)] / 100);
    }
    
    function getMacrosComida(array $macros, string $comida): array{
        $macrosComida = [];
        foreach ($macros as $nombreMacro=>$macro) {
            $macrosComida[$nombreMacro]['min']= $macro['min'] * ($_SESSION['usuario']['porcent_' . strtolower($comida)] / 100);
            $macrosComida[$nombreMacro]['max']= $macro['max'] * ($_SESSION['usuario']['porcent_' . strtolower($comida)] / 100);
        }
        return $macrosComida;
    }
    
    function getCaloriasAndMealPlan(int $numComidas, string $dieta, string $date) {
        $dia= date("j-n-Y", strtotime($date));
        $calorias['desayuno'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_breakfast'] / 100);
        $calorias['brunch'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_snack'] / 100);
        $calorias['comida'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_lunch'] / 100);
        $calorias['brunch'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_brunch'] / 100);
        $calorias['cena'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_dinner'] / 100);
        $mealPlan = [];
        if ($numComidas == 3) {
            $mealPlan['desayuno'] = $this->getComida($dieta, 'Breakfast', $calorias['desayuno'], $dia);
            $mealPlan['comida'] = $this->getComida($dieta, 'Lunch', $calorias['comida'],$dia);
            $mealPlan['cena'] = $this->getComida($dieta, 'Dinner', $calorias['cena'],$dia);
        }
        if ($numComidas == 4) {
            $mealPlan['desayuno'] = $this->getComida($dieta, 'Breakfast', $calorias['desayuno'],$dia);
            $mealPlan['brunch'] = $this->getComida($dieta, 'Snack', $calorias['brunch'],$dia);
            $mealPlan['comida'] = $this->getComida($dieta, 'Lunch', $calorias['comida'],$dia);
            $mealPlan['cena'] = $this->getComida($dieta, 'Dinner', $calorias['cena'],$dia);
        }
        if ($numComidas == 5) {
            $mealPlan['desayuno'] = $this->getComida($dieta, 'Breakfast', $calorias['desayuno'],$dia);
            $mealPlan['brunch'] = $this->getComida($dieta, 'Snack', $calorias['brunch'],$dia,$dia);
            $mealPlan['comida'] = $this->getComida($dieta, 'Lunch', $calorias['comida'],$dia);
            $mealPlan['merienda'] = $this->getComida($dieta, 'Teatime', $calorias['merienda'],$dia);
            $mealPlan['cena'] = $this->getComida($dieta, 'Dinner', $calorias['cena'],$dia);
        }
    }

    function getAllNutrientes(array $mealplan): array {
        $nutrientesTotales = [];
        $nut = [];
        foreach ($mealplan as $comida) {
            array_push($nutrientesTotales, json_decode($comida['nutrientes'], true));
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
