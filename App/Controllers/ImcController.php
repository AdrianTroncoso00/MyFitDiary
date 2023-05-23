<?php

namespace App\Controllers;

class ImcController extends \App\Core\BaseController {

    const PORCENTAJES_IMC_ADULTOS = ([
        'bajo' => 18.5,
        'saludable' => 24.9,
        'sobrepeso' => 29.9,
        'obesidad1' => 34.9,
        'obesidad2' => 39.9
    ]);
    const PORCENTAJES_IMC_NIÑOS = ([
        'bajo' => 3,
        'saludable' => 85,
        'sobrepeso' => 97
    ]);
    const SEDENTARIO = 1;
    const MODERADO = 2;
    const ACTIVA = 3;
    const MUY_ACTIVO = 4;
    const FACTOR_ACTIVIDAD_SEDENTARIO = 1.2;
    const FACTOR_ACTIVIDAD_MODERADA = 1.375;
    const FACTOR_ACTIVIDAD_ACTIVA = 1.55;
    const FACTOR_ACTIVIDAD_MUY_ACTIVO = 1.725;
    const FACTOR_CALORICO = 400;
    const PORCENTAJES_GRASA_HOMBRE_ADOLESCENTE = ([
        'bajo' => 10,
        'normal' => 24.5,
        'alto' => 26.9
    ]);
    const PORCENTAJES_GRASA_HOMBRE_JOVEN = ([
        'bajo' => 8,
        'normal' => 19.9,
        'alto' => 24.9
    ]);
    const PORCENTAJES_GRASA_HOMBRE_MADURO = ([
        'bajo' => 11,
        'normal' => 21.9,
        'alto' => 27.9
    ]);
    const PORCENTAJES_GRASA_HOMBRE_MAYOR = ([
        'bajo' => 13,
        'normal' => 24.9,
        'alto' => 29.9
    ]);
    const NUMERO_COMIDAS_DIARIAS = [3, 4, 5];
    const METAS = ['Perder Peso', 'Mantener Peso', 'Aumentar Masa Muscular'];

    function showFormIMC() {
        $modeloDietas = new \App\Models\DietasModel();
        $modeloActFis = new \App\Models\ActFisicaModel();
        $modeloAlergenos = new \App\Models\AlergenosModel();
        $data['dietas'] = $modeloDietas->getAllDietas();
        $data['actFis'] = $modeloActFis->getAllActFisica();
        $data['num_comidas'] = self::NUMERO_COMIDAS_DIARIAS;
        $data['alergenos'] = $modeloAlergenos->getAll();
        return view('IMCform.view.php', $data);
    }

    function mostrarResForm() {
        $data = [];
        //$errores = $this->checkForm($_POST);
        $input = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        //if (count($errores) == 0) {
            $imc = $this->calcularImc($_POST['peso'], $_POST['altura']);
            $calorias = $this->getTMB($_POST);
            $data['forma_fisica'] = $this->formaFisica($imc, $_POST['edad']);
            $res = array_merge($_POST, $calorias);
            $res['imc'] = $imc;
            $m = array_merge($_SESSION['usuario'], $res);
            $modelo = new \App\Models\InfoUsuariosModel();
            if ($modelo->addInfoUsuario($res, $_SESSION['usuario']['id'])) {
                return redirect()->to('/meal-plan');
            }
//            $data['input'] = $input;
//            $data['errores'] = $errores;
//            $data['input'] = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
//            return view('IMCform.view.php', $data);
        //} else {
//            $modeloActividad = new \App\Models\ActFisicaModel();
//
//            $act_fisica = $modeloActividad->getAllId();
//            var_dump($errores);
//            var_dump($_POST);
//            $data['input'] = $input;
//            $data['errores'] = $errores;
//            $data['input'] = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
//            $modeloActFis = new \App\Models\ActFisicaModel();
//            $data['actFis'] = $modeloActFis->getAllActFisica();
//            $data['num_comidas'] = self::NUMERO_COMIDAS_DIARIAS;
//            $modeloDietas = new \App\Models\DietasModel();
//            $data['dietas'] = $modeloDietas->getAllDietas();
//            return view('IMCform.view.php', $data);
        //}
    }

    function calcularImc(float $peso, float $altura): float {
        $altura = $altura / 100;
        return round(($peso / (pow($altura, 2))), 2);
    }

    function getTMB(array $datos): array {
        if (isset($datos['genero']) && $datos['genero'] == 'masculino') {
            $tbd = 88.362 + (13.397 * $datos['peso']) + (4.799 * $datos['altura']) - (5.677 * $datos['edad']);
        } else {
            $tbd = 447.593 + (9.247 * $datos['peso']) + (3.098 * $datos['altura']) - (4.330 * $datos['edad']);
        }
        if ($datos['actividad'] == self::SEDENTARIO) {
            $factorActividad = self::FACTOR_ACTIVIDAD_SEDENTARIO;
        }
        if ($datos['actividad'] == self::MODERADO) {
            $factorActividad = self::FACTOR_ACTIVIDAD_MODERADA;
        }
        if ($datos['actividad'] == self::ACTIVA) {
            $factorActividad = self::FACTOR_ACTIVIDAD_ACTIVA;
        }
        if ($datos['actividad'] == self::MUY_ACTIVO) {
            $factorActividad = self::FACTOR_ACTIVIDAD_MUY_ACTIVO;
        }
        $caloriasDiarias = round($tbd * $factorActividad);
        if ($datos['meta'] == 'Perder Peso') {
            $caloriasObjetivo = round($caloriasDiarias - self::FACTOR_CALORICO);
        } else if ($datos['meta'] == 'Aumentar Masa Muscular') {
            $caloriasObjetivo = round($caloriasDiarias + self::FACTOR_CALORICO);
        } else {
            $caloriasObjetivo = round($caloriasDiarias);
        }
        return ([
            'tmb' => round($tbd),
            'caloriasMantenimiento' => $caloriasDiarias,
            'caloriasObjetivo' => $caloriasObjetivo
        ]);
    }
    
    function formaFisica(float $imc, int $edad): string {
        if ($edad >= 18) {
            if ($imc <= self::PORCENTAJES_IMC_ADULTOS['bajo']) {
                return "Su IMC($imc) es menor de " . self::PORCENTAJES_IMC_ADULTOS['bajo'] . ": En este rango de IMC, es posible que la persona tenga una ingesta calórica "
                        . "insuficiente o un problema de salud que le impida mantener un peso saludable. Un bajo peso también puede aumentar el riesgo de enfermedades y problemas de salud.";
            }
            if ($imc > self::PORCENTAJES_IMC_ADULTOS['bajo'] && $imc <= self::PORCENTAJES_IMC_ADULTOS['saludable']) {
                return "Su IMC($imc) se encuentra entre " . self::PORCENTAJES_IMC_ADULTOS['bajo'] . " y " . round(self::PORCENTAJES_IMC_ADULTOS['saludable']) . ": Un IMC en este rango se considera saludable y se asocia con un menor riesgo de "
                        . "enfermedades crónicas como la diabetes y la enfermedad cardiovascular. Es importante mantener un peso saludable a través de una dieta equilibrada y actividad física regular.";
            }
            if ($imc > self::PORCENTAJES_IMC_ADULTOS['saludable'] && $imc <= self::PORCENTAJES_IMC_ADULTOS['sobrepeso']) {
                return "Su IMC($imc) se encuentra entre " . self::PORCENTAJES_IMC_ADULTOS['saludable'] . " y " . round(self::PORCENTAJES_IMC_ADULTOS['sobrepeso']) . ": En este rango de IMC, la persona tiene un exceso de peso que puede aumentar el riesgo de enfermedades "
                        . "crónicas como la diabetes, la enfermedad cardiovascular y algunos tipos de cáncer. Es importante llevar a cabo cambios en el estilo de vida como una alimentación saludable y la actividad física para reducir el peso y mantener la salud.";
            }
            if ($imc > self::PORCENTAJES_IMC_ADULTOS['sobrepeso'] && $imc <= self::PORCENTAJES_IMC_ADULTOS['obesidad1']) {
                return "Su IMC($imc) se encuentra entre " . self::PORCENTAJES_IMC_ADULTOS['sobrepeso'] . " y " . round(self::PORCENTAJES_IMC_ADULTOS['obesidad1']) . ": Un IMC en este rango se considera obesidad de grado leve y aumenta el riesgo de "
                        . "enfermedades crónicas. Se recomienda realizar cambios en el estilo de vida para reducir el peso y mejorar la salud.";
            }
            if ($imc > self::PORCENTAJES_IMC_ADULTOS['obesidad1'] && $imc <= self::PORCENTAJES_IMC_ADULTOS['obesidad2']) {
                return "Su IMC($imc) se encuentra entre " . self::PORCENTAJES_IMC_ADULTOS['obesidad1'] . " y " . round(self::PORCENTAJES_IMC_ADULTOS['obesidad2']) . ": En este rango de IMC, la persona tiene una obesidad de grado moderado que aumenta el riesgo "
                        . "de enfermedades crónicas y puede requerir intervenciones médicas y cambios importantes en el estilo de vida para reducir el peso y mejorar la salud.";
            }
            if ($imc > self::PORCENTAJES_IMC_ADULTOS['obesidad2']) {
                return "Su IMC($imc) es mayor de " . round(self::PORCENTAJES_IMC_ADULTOS['obesidad2']) . ": Un IMC en este rango se considera una obesidad de grado severo y aumenta significativamente el riesgo de enfermedades crónicas. Es importante buscar la ayuda"
                        . " de un profesional médico para abordar la obesidad y reducir el peso de manera segura y efectiva.";
            }
        } else {
            if ($imc <= self::PORCENTAJES_IMC_NIÑOS['bajo']) {
                return "Su IMC($imc) es menor de " . self::PORCENTAJES_IMC_NIÑOS['bajo'] . ": Los niños con un IMC en este percentil tienen un peso inferior al promedio para su edad y género. Es importante que los padres o cuidadores "
                        . "consulten con un profesional médico para determinar si el bajo peso es una preocupación médica y trabajar en planes para mejorar la nutrición y el crecimiento.";
            }
            if ($imc > self::PORCENTAJES_IMC_NIÑOS['bajo'] && $imc <= self::PORCENTAJES_IMC_NIÑOS['saludable']) {
                return "Su IMC($imc) se encuentra entre " . self::PORCENTAJES_IMC_NIÑOS['bajo'] . " y " . round(self::PORCENTAJES_IMC_NIÑOS['saludable']) . ": Los niños con un IMC en este percentil tienen un peso saludable para su edad y género. "
                        . "Se recomienda seguir una dieta saludable y mantener la actividad física para mantener un peso saludable a largo plazo.";
            }
            if ($imc > self::PORCENTAJES_IMC_NIÑOS['saludable'] && $imc <= self::PORCENTAJES_IMC_NIÑOS['sobrepeso']) {
                return "Su IMC($imc) se encuentra entre " . self::PORCENTAJES_IMC_NIÑOS['saludable'] . " y " . round(self::PORCENTAJES_IMC_NIÑOS['sobrepeso']) . ": Los niños con un IMC en este percentil tienen un peso superior al promedio para su edad y género. "
                        . "El sobrepeso en la infancia puede aumentar el riesgo de problemas de salud a largo plazo, como enfermedades cardiovasculares, diabetes y problemas de salud mental. Se recomienda seguir una dieta saludable y mantener la actividad física para reducir el peso y mejorar la salud.";
            }
            if ($imc > self::PORCENTAJES_IMC_NIÑOS['sobrepeso']) {
                return "Su IMC($imc) es mayor de " . self::PORCENTAJES_IMC_NIÑOS['sobrepeso'] . ": Los niños con un IMC en este percentil tienen un peso significativamente superior al promedio para su edad y género. La obesidad en la infancia puede aumentar el riesgo de problemas de salud a largo plazo, "
                        . "como enfermedades cardiovasculares, diabetes y problemas de salud mental. Es importante buscar la ayuda de un profesional médico para abordar la obesidad y reducir el peso de manera segura y efectiva.";
            }
        }
    }

    function checkForm(array $datos): array {
        $errores = [];
        $modeloActividad = new \App\Models\ActFisicaModel();
        $modeloDietas = new \App\Models\DietasModel();
        $act_fisica = $modeloActividad->getAllId();
        $dietas = $modeloDietas->getAllDietas();

        if (empty($datos['genero'])) {
            $errores['genero'] = 'este campo es obligatorio';
        } else {
//            if($datos['genero'] !=='masculino' || $datos['genero']!=='femenino'){
//                $errores['genero']='El genero solo puede ser masculino o femenino';
//            }      
        }
        if (!isset($datos['edad'])) {
            $errores['edad'] = 'el campo edad es obligatorio';
        } else {
            if (!is_numeric($datos['edad'])) {
                $errores['edad'] = 'tiene que ser un valor numerico';
            }
            if ($datos['edad'] < 12 || $datos['edad'] > 100) {
                $errores['edad'] = 'la edad tiene que estar comprendida entre 12 y 100 años';
            }
        }
        if (!isset($datos['peso'])) {
            $errores['peso'] = 'el campo peso es obligatorio';
        } else {
            if (!is_numeric($datos['peso'])) {
                $errores['edad'] = 'tiene que ser un valor numerico';
            }
            if ($datos['peso'] < 20) {
                $errores['edad'] = 'introduzca un peso en KG valido';
            }
        }
        if (!isset($datos['altura'])) {
            $errores['altura'] = 'la altura es obligatoria';
        } else {
            if (!is_numeric($datos['altura'])) {
                $errores['edad'] = 'tiene que ser un valor numerico';
            }
            if ($datos['altura'] <= 0) {
                $errores['edad'] = 'introduzca una altura valida';
            }
        }
        if (empty($datos['actividad'])) {
            $errores['actividad'] = 'este campo es obligatorio';
        } else {
//            if(!in_array($datos['actividad'], $act_fisica)){
//                $errores['actividad']='tiene que seleccionar una opcion correcta';
//            }
        }
        if (empty($datos['meta'])) {
            $errores['meta'] = 'este campo es obligatorio';
        } else {
            if (!in_array($datos['meta'], self::METAS)) {
                $errores['meta'] = 'tiene que seleccionar una opcion correcta';
            }
        }
        if (empty($datos['num_comidas'])) {
            $errores['num_comidas'] = 'inserte el numero de comidas que quiere hacer al día';
        } else {
            if (!in_array($datos['num_comidas'], self::NUMERO_COMIDAS_DIARIAS)) {
                $errores['num_comidas'] = 'el numero de comidas no es valido';
            }
        }
        if (empty($datos['dieta'])) {
            $errores['dieta'] = 'inserte la dieta que quiere seguir';
        } else {
//            if(!in_array($datos['dieta'], $dietas)){
//                $errores['dieta']='la dieta introducida no es valida';
//            }
        }
        if(empty($datos['porcent_desayuno'])){
            $errores['porcent_desayuno']='Introduce el porcentaje de las calorias totales que quiere consumir en el desayuno';
        }else{
            if(!is_numeric($datos['porcent_desayuno']) || ($datos['porcent_desayuno'])<1 || $datos['porcent_desayuno']>100){
                $errores['porcent_desayuno']='El porcentaje tiene que ser un valor numerico comprendido entre el 1 y el 100';
            }
        }
        if(empty($datos['porcent_comida'])){
            $errores['porcent_comida']='Introduce el porcentaje de las calorias totales que quiere consumir en la comida';
        }else{
            if(!is_numeric($datos['porcent_comida']) || ($datos['porcent_comida'])<1 || $datos['porcent_comida']>100){
                $errores['porcent_comida']='El porcentaje tiene que ser un valor numerico comprendido entre el 1 y el 100';
            }
        }
        if(empty($datos['porcent_cena'])){
            $errores['porcent_cena']='Introduce el porcentaje de las calorias totales que quiere consumir en la cena';
        }else{
            if(!is_numeric($datos['porcent_cena']) || ($datos['porcent_cena'])<1 || $datos['porcent_cena']>100){
                $errores['porcent_cena']='El porcentaje tiene que ser un valor numerico comprendido entre el 1 y el 100';
            }
        }
        if($datos['porcent_desayuno']+$datos['porcent_comida']+$datos['porcent_cena'] !==100){
            $errores['porcent_cena']='la suma de todos los porcentajes de las comidas tiene que ser el 100%';
        }
        return $errores;
    }

}
