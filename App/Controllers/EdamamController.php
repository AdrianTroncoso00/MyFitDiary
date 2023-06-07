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

    function generarQuery(string $dieta, string $mealType, array $alergenos, array $tiempoCocina=[]): string {
        $stringAlergenos = !empty($alergenos) ? '&healt='.implode('&healt=', $alergenos) : '';
        $stringTiempoCocina = !empty($tiempoCocina) ? '&time='.implode('&time=', $tiempoCocina) : '';
        return self::URL_CONSULTA_BUSCADOR . '&diet=' . $dieta . '&mealType=' . $mealType . $stringAlergenos.$stringTiempoCocina.'&health=alcohol-free&rand=true' . self::URL_CONSULTA_FIELD;
    }

    public function getSemanaActual(): array {
        $diaSemana = date("w");
        $tiempoDeInicioDeSemana = strtotime("-" . ($diaSemana - 1) . " days"); # Restamos -X days
        $fechaInicioSemana = date("Y-m-d", $tiempoDeInicioDeSemana);
        $tiempoDeFinDeSemana = strtotime("+7 days", $tiempoDeInicioDeSemana); # Sumamos +X days, pero partiendo del tiempo de inicio
        $fechaFinSemana = date("Y-m-d", $tiempoDeFinDeSemana);
        $semana = [];
        while ($fechaInicioSemana < $fechaFinSemana) {
            array_push($semana, $fechaInicioSemana);
            $fechaInicioSemana = date("Y-m-d", strtotime($fechaInicioSemana . "+1 day"));
        }
        return $semana;
    }

    function getRequestCurlArray(string $query, bool $buscador=false) {
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
                    return $buscador ? $recetas : $recetas['hits'];
                default:
                    echo 'Unexpected HTTP code: ', $http_code, "\n";
            }
        }
        curl_close($curl);
    }
    
    function getNutrientesSemanaConsumido():?array{
        $modelo = new \App\Models\ComidasModel();
        $semana = $this->getSemanaActual();
        $nutrientesDia =[];
        foreach ($semana as $dia) {
            $mealDiario = $modelo->getMealPlanDiaria($_SESSION['usuario']['id'], $dia);
            if (!is_null($mealDiario)) {
                $mealDiarioModificado = $this->transformarArray($mealDiario);
                $nutrientesDia[$dia] = $this->getAllNutrientes2($mealDiarioModificado);
            }
        }
        return empty($nutrientesDia) ? null : $nutrientesDia;
    }
    
    function showNutrientesSemana() {
        $nutrientes = $this->getNutrientesSemanaConsumido();
        if(!is_null($nutrientes)){
            $nutrientesSemana = $this->getAllNutrientesSemana($nutrientes);
            $data = $this->getData($nutrientesSemana);
            return view('left-menu.view.php').view('nutrientes-semana.view.php', $data);
        }else{
            $data['nutrientesTotales']=[];
            return view('left-menu.view.php').view('nutrientes-semana.view.php',$data);
        }
        
    }

    function transformarArray(array $mealplan): array {
        $array = [];
        foreach ($mealplan as  $comida) {
            $array[substr($comida['nombre_comida'], 0, -1)][] = ([
                'id_receta' => $comida['id_comida'],
                'url' => $comida['url'],
                'label' => $comida['label'],
                'calorias' => $comida['calorias'],
                'yield' => $comida['yield'],
                'image' => $comida['image'],
                'totalTime' => $comida['totaltime'],
                'cuisineType' => $comida['cuisinetype'],
                'ingredientes' => json_decode($comida['ingredients'], true),
                'nutrientes' => json_decode($comida['nutrientes'], true),
                'nombre_comida' => $comida['nombre_comida'],
                'healthLabels' => json_decode($comida['healthlabels'], true),
                'calorias_comida' => $this->getCaloriasComida(substr($comida['nombre_comida'], 0, -1)) / 2
            ]);
        }
        return $array;
    }

    function showMealPlanDiario() {
        $dia = date("Y-m-d");
        var_dump($_SESSION);
        $modelo = new \App\Models\ComidasModel();
        $mealPlanDia = $modelo->getMealPlanDiaria($_SESSION['usuario']['id'], $dia);
        if (!is_null($mealPlanDia)) {
            $nutrientesTotales = $this->getAllNutrientes($mealPlanDia);
            $data=$this->getData($nutrientesTotales);
            $data['fecha'] = $dia;
            $data['mealPlan'] = $this->transformarArray($mealPlanDia);
            $data['input']['fecha'] = date("d/m/Y", strtotime($dia));
            return view('left-menu.view.php') . view('meal-plan.view.php', $data);
        } else {
            return view('left-menu.view.php') . view('p.view.php');
        }
    }

    function getMealPlanDiario() {
        $semana = $this->getSemanaActual();
        var_dump($semana);
        $dia = date("Y-m-d", strtotime($_POST['fecha']));
        $diaActual = date("Y-m-d");
        if(in_array($dia, $semana)){
            $modelo = new \App\Models\ComidasModel();
            $mealPlanDia = $modelo->getMealPlanDiaria($_SESSION['usuario']['id'], $dia);
            if (!is_null($mealPlanDia)) {
                $mealPlan = $this->transformarArray($mealPlanDia);
                $nutrientesTotales = $this->getAllNutrientes($mealPlanDia);
            } else {
                if($diaActual > $dia){
                    $data['error'] = 'Para generar el meal plan tiene que introducir un dia mayor al actual dentro de la semana actual';
                    return view('left-menu.view.php') . view('p.view.php', $data);
                }
                $this->getCaloriasAndMealPlan($_SESSION['usuario']['num_comidas'], $_SESSION['usuario']['nombre_dieta'],$dia);
                $mealPlanSinProcesar = $modelo->getMealPlanDiaria($_SESSION['usuario']['id'], $dia);
                $mealPlan = $this->transformarArray($mealPlanSinProcesar);
                $nutrientesTotales = $this->getAllNutrientes($mealPlanSinProcesar);    
            }
            $data = $this->getData($nutrientesTotales);
            $data['fecha'] = $dia;
            $data['mealPlan'] = $mealPlan;
            $data['nutrientesTotales'] = $nutrientesTotales;
            return view('left-menu.view.php') . view('meal-plan.view.php', $data);
        }else{
            $data['error'] = 'Solo puede generar el meal plan en la semana actual';
            return view('left-menu.view.php') . view('p.view.php', $data);
        }
    }

    function regenerarComidaEntera(string $mealType, string $date) {
        $fecha = date("Y-m-d", strtotime($date));
        $modeloComida = new \App\Models\ComidasModel();
        $calorias = $this->getCaloriasComida($mealType);
        $comidas = $this->getComida($_SESSION['usuario']['nombre_dieta'], $mealType, $calorias, $fecha);
        foreach ($comidas as $key => $comida) {
            if (!$modeloComida->modificarComida($_SESSION['usuario']['id'], $fecha, ($mealType . ($key + 1)), $comida)) {
                $_SESSION['usuario']['error'] = 'No se ha podido regenerar la comida';
                redirect()->to('meal-plan');
                unset($_SESSION['usuario']['error']);
            }
        }
        return redirect()->to('meal-plan');
    }

    function regenerarComidaEspecifica(int $id_receta, string $nombreComida, int $caloriasComida, string $date) {
        $dia = date("Y-m-d", strtotime($date));
        $modeloComida = new \App\Models\ComidasModel();
        $receta = $this->getComida($_SESSION['usuario']['nombre_dieta'], substr($nombreComida, 0, -1), $caloriasComida, $dia);
        if ($modeloComida->modificarRecetaEspecifica($id_receta, $receta[0])) {
            return redirect()->to('/meal-plan');
        } else {
            $_SESSION['usuario']['error'] = 'Ha ocurrido un error al regenerar la comida';
            redirect()->to('/meal-plan');
            unset($_SESSION['usuario']['error']);
        }
    }

    function eliminarRecetaEspecifica(int $id_comida) {
        $modelo = new \App\Models\ComidasModel();
        if ($modelo->deleteComidaEspecifica($id_comida)) {
            return redirect()->to('/meal-plan');
        } else {
            $data['error'] = 'Ha ocurrido un error al eliminar la comida';
            return view('left-menu.view.php') . view('meal-plan.view.php', $data);
        }
    }

    function getComida(string $dieta, string $mealType, int $calorias, string $dia): ?array {
        $diaActual=date("Y-m-d", strtotime($dia));
        $semana = $this->getSemanaActual();
        $modelo = new \App\Models\ComidasModel();
        $query = $this->generarQuery($dieta, $mealType, $_SESSION['usuario']['alergenos']);
        $recetas = $this->getRequestCurlArray($query);
        $numComidas = $calorias < 450 ? 1 : 2;
        if ($calorias <= 450) {
            $receta = $this->obtenerRecetasSinRepetir($recetas, $mealType, $calorias, $numComidas, $semana[0], $semana[6]);
            if ($modelo->existeComidaDia($diaActual, $_SESSION['usuario']['id'], $mealType)) {
                return $receta;
            } else {
                if ($modelo->addComida($_SESSION['usuario']['id'], $receta[0], ($mealType . '1'), $diaActual)) {
                    return $receta;
                }
            }
        } else {
            $recetasProcesadas = $this->obtenerRecetasSinRepetir($recetas, $mealType, $calorias, $numComidas, $semana[0], $semana[6]);
            if ($modelo->existeComidaDia($diaActual, $_SESSION['usuario']['id'], $mealType)) {
                return $recetasProcesadas;
            } else {
                if ($modelo->addComida($_SESSION['usuario']['id'], $recetasProcesadas[0], ($mealType . '1'), $diaActual) && $modelo->addComida($_SESSION['usuario']['id'], $recetasProcesadas[1], ($mealType . '2'), $diaActual)) {
                    return $recetasProcesadas;
                }
            }
        }
    }

    function obtenerRecetasSinRepetir(array $recetas, string $mealType, int $calorias, int $numComidas, string $diaInicioSemana, string $diaFinalSemana): array {
        $modelo = new \App\Models\ComidasModel();
        $randoms = range(0, count($recetas) - 1);
        $recetasCopia = $recetas;
        $recetasSemanaComidas = $modelo->getComidasSemana($_SESSION['usuario']['id'], strtolower($mealType), $diaInicioSemana, $diaFinalSemana);
        $nums = [];
        $labels=[];
        $recetasInput = [];
        $posiciones = [];
        var_dump(count($recetas));
        if (!is_null($recetasSemanaComidas)) {
            foreach ($recetasSemanaComidas as $recetaSemana) {
                foreach ($recetas as $key => $receta) {
                    if ($receta['recipe']['label'] === $recetaSemana['label'] || $receta['recipe']['calories'] < 1) {
                        unset($recetas[$key]);
                        array_push($nums, $key);
                        array_push($labels, $receta['recipe']['label']);
                    }
                }
            }
            var_dump($labels);
            var_dump(count($recetas));
            $posiciones = array_diff($randoms, $nums);
            if (count($posiciones)>$numComidas) {
                shuffle($posiciones);
            } else { 
                $recetas = $recetasCopia;
                $posiciones = $randoms;
                shuffle($posiciones);
            }
        } else {
            $posiciones = $randoms;
            shuffle($posiciones);
        }
        $caloriasInput = $numComidas == 1 ? $calorias : $calorias / 2;
        while (count($recetasInput) < ($numComidas)) {
            $random = $posiciones[rand(0, count($posiciones) - 1)];
            $receta = $this->modifyReceta($recetas[$random], $caloriasInput, $mealType);
            if (!in_array($receta, $recetasInput)) {
                if ($receta['calorias'] > 1) {
                    array_push($recetasInput, $receta);
                }
            }
        }
        return $recetasInput;
    }

    function modifyReceta(array $receta, float $calorias, string $mealType): array {
        $nuevaReceta = [];
        $caloriasRacion = $receta['recipe']['calories'] == 0 ? 0 : round(($receta['recipe']['calories'] / $receta['recipe']['yield']), 0);
        $yield = $caloriasRacion == 0 ? 1 : round(($calorias / $caloriasRacion), 0);
        $nuevaReceta['url'] = $receta['recipe']['url'];
        $nuevaReceta['image'] = $receta['recipe']['image'];
        $nuevaReceta['label'] = $receta['recipe']['label'];
        $nuevaReceta['totalTime'] = $receta['recipe']['totalTime'];
        $nuevaReceta['cuisineType'] = $receta['recipe']['cuisineType'];
        $nuevaReceta['calorias'] = round(($caloriasRacion * $yield), 0);
        $nuevaReceta['yield'] = $yield;
        $nuevaReceta['nombre_comida'] = $mealType;
        $nuevaReceta['mealType'] = $receta['recipe']['mealType'];
        $nuevaReceta['healthLabels'] = $receta['recipe']['healthLabels'];
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
            foreach ($receta['recipe']['totalNutrients'] as $key=>$nutriente) {
                $nutriente['quantity'] = round($nutriente['quantity'], 0);
                $nuevaReceta['nutrientes'][$key] = $nutriente;
            }
            foreach ($receta['recipe']['ingredients'] as $key => $ingrediente) {
                $nuevaReceta['ingredientes'][$key]['quantity'] = $ingrediente['quantity'];
                $nuevaReceta['ingredientes'][$key]['measure'] = $ingrediente['measure'];
                $nuevaReceta['ingredientes'][$key]['food'] = $ingrediente['food'];
                $nuevaReceta['ingredientes'][$key]['image'] = $ingrediente['image'];
                $nuevaReceta['ingredientes'][$key]['stringIngrediente'] = $ingrediente['text'];
            }
        }
        return $nuevaReceta;
    }

    function getCaloriasComida(string $comida): int {
        return $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_' . strtolower($comida)] / 100);
    }

    function getCaloriasAndMealPlan(int $numComidas, string $dieta,string $diaActual) {
        $calorias['desayuno'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_breakfast'] / 100);
        $calorias['brunch'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_snack'] / 100);
        $calorias['comida'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_lunch'] / 100);
        $calorias['brunch'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_brunch'] / 100);
        $calorias['cena'] = $_SESSION['usuario']['calorias_objetivo'] * ($_SESSION['usuario']['porcent_dinner'] / 100);
        $mealPlan = [];
        if ($numComidas == 3) {
            $mealPlan['desayuno'] = $this->getComida($dieta, 'Breakfast', $calorias['desayuno'],$diaActual);
            $mealPlan['comida'] = $this->getComida($dieta, 'Lunch', $calorias['comida'],$diaActual);
            $mealPlan['cena'] = $this->getComida($dieta, 'Dinner', $calorias['cena'],$diaActual);
        }
        if ($numComidas == 4) {
            $mealPlan['desayuno'] = $this->getComida($dieta, 'Breakfast', $calorias['desayuno'],$diaActual);
            $mealPlan['brunch'] = $this->getComida($dieta, 'Brunch', $calorias['brunch'],$diaActual);
            $mealPlan['comida'] = $this->getComida($dieta, 'Lunch', $calorias['comida'],$diaActual);
            $mealPlan['cena'] = $this->getComida($dieta, 'Dinner', $calorias['cena'],$diaActual);
        }
        if ($numComidas == 5) {
            $mealPlan['desayuno'] = $this->getComida($dieta, 'Breakfast', $calorias['desayuno'],$diaActual);
            $mealPlan['brunch'] = $this->getComida($dieta, 'Brunch', $calorias['brunch'],$diaActual);
            $mealPlan['comida'] = $this->getComida($dieta, 'Lunch', $calorias['comida'],$diaActual);
            $mealPlan['merienda'] = $this->getComida($dieta, 'Snack', $calorias['merienda'],$diaActual);
            $mealPlan['cena'] = $this->getComida($dieta, 'Dinner', $calorias['cena'],$diaActual);
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

    function getAllNutrientes2(array $mealplan): array {
        $nutrientesTotales = [];
        $nut = [];
        foreach ($mealplan as $comida) {
            foreach ($comida as $value) {
                array_push($nutrientesTotales, $value['nutrientes']);
            }
        }
        foreach ($nutrientesTotales as $key => $nutriente) {
            if (is_array($nutriente)) {
                foreach ($nutriente as $infoNutriente) {
                    $nut[$infoNutriente['label']]['cantidad'][$key] = round($infoNutriente['quantity'],0);
                    $nut[$infoNutriente['label']]['unidad'] = $infoNutriente['unit'];
                    $nut[$infoNutriente['label']]['cantidadTotal'] = round(array_sum($nut[$infoNutriente['label']]['cantidad']),0);
                }
            } else {
                unset($nutriente);
            }
        }
        return $nut;
    }

    function getAllNutrientesSemana(array $mealPlanSemana): array {
        $nutrientesSemana = [];
        foreach ($mealPlanSemana as $mealPlan) {
            foreach ($mealPlan as $nombre => $nutriente) {
                $nutrientesSemana[$nombre]['cantidad'][] = round($nutriente['cantidadTotal'],0);
                $nutrientesSemana[$nombre]['unidad'] = $nutriente['unidad'];
                $nutrientesSemana[$nombre]['cantidadTotal'] = round(array_sum($nutrientesSemana[$nombre]['cantidad']),0);
            }
        }
        return $nutrientesSemana;
    }
    
    function getData(array $nutrientesTotales):array{
        $data['nutrientesTotales']=$nutrientesTotales;
        $data['etiquetas'] = ['Proteinas', 'Grasas', 'Carbohidratos'];
        $data['valores_etiquetas'] = [round($nutrientesTotales['Protein']['cantidadTotal'], 0), round($nutrientesTotales['Fat']['cantidadTotal'], 0), round($nutrientesTotales['Carbs']['cantidadTotal'], 0)];
        $data['chart_colors'] = [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 205, 86)'];
        return $data;
    }

}
