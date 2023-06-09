<?php

namespace App\Controllers;

use App\Controllers\SessionController;

class ImcController extends \App\Core\BaseController {

    const GENEROS = ['masculino', 'femenino'];
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
        var_dump($_SESSION);
        $modeloDietas = new \App\Models\DietasModel();
        $modeloActFis = new \App\Models\ActFisicaModel();
        $modeloAlergenos = new \App\Models\AlergenosModel();
        $data['metas'] = self::METAS;
        $data['generos'] = self::GENEROS;
        $data['editar']=false;
        $data['dietas'] = $modeloDietas->getAllDietas();
        $data['actFis'] = $modeloActFis->getAllActFisica();
        $data['num_comidas'] = self::NUMERO_COMIDAS_DIARIAS;
        $data['alergenos'] = $modeloAlergenos->getAll();
        return view('IMCform.view.php', $data);
    }

    function mostrarResForm() {
        $data = [];
        $modeloSesion = new \App\Models\SessionModel();
        $modeloInfoUsuarios = new \App\Models\InfoUsuariosModel();
        $errores = $this->checkForm($_POST);
        var_dump($_POST);
        var_dump($errores);
        $input = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        if (count($errores) == 0) {
            $imc = $this->calcularImc($_POST['peso'], $_POST['estatura']);
            $calorias = $this->getTMB($_POST);
            $data['forma_fisica'] = $this->formaFisica($imc, $_POST['edad']);
            $alergenos = empty($_POST['alergenos']) ? '' : $_POST['alergenos'];
            var_dump($_SESSION);
            unset($_POST['alergenos']);
            $modeloRelAlergenos = new \App\Models\RelAlergenosModel();
            $add = array_merge($_POST,$calorias);
            $add['imc']=$imc;
            $add['id_usuario']=$_SESSION['usuario']['id'];
            $addReverse= array_reverse($add);
            var_dump($addReverse);
            if ($modeloInfoUsuarios->save($addReverse)) {
                var_dump('hola');
                $infoUsuario = $modeloSesion->getAllUsuario($_SESSION['usuario']['id']);
                var_dump($infoUsuario);
                $this->session->set('usuario', $infoUsuario);
                var_dump($_SESSION);
                if (!empty($alergenos) && is_array($alergenos)) {
                    foreach ($alergenos as $alergeno) {
                        $modeloRelAlergenos->save(['id_usuario'=>$_SESSION['usuario']['id'],'alergeno'=>$alergeno]);
                    }
                }
                return redirect()->to('/meal-plan');
            }
            return redirect()->to('/imc')->with('error', 'Ha ocurrido un error al guardar los datos del formulario');
        } else {
            $data['errores'] = $errores;
            $data['input'] = $input;
            $this->showFormIMC($errores);
        }
    }

    function showFormEditar() {
        $modeloDietas = new \App\Models\DietasModel();
        $modeloActFis = new \App\Models\ActFisicaModel();
        $modeloAlergenos = new \App\Models\AlergenosModel();
        $modeloRelAlergenos = new \App\Models\RelAlergenosModel();
        $modelo = new \App\Models\InfoUsuariosModel();
        $infoUsuario = $modelo->getAllInfoUsuario($_SESSION['usuario']['id']);
        $input = $infoUsuario[0];
        $input['alergenos'] = $modeloRelAlergenos->getAlergenosUsuario($_SESSION['usuario']['id']);
        $data['input'] = $input;
        $data['metas'] = self::METAS;
        $data['editar'] = true;
        $data['generos'] = self::GENEROS;
        $data['dietas'] = $modeloDietas->getAllDietas();
        $data['actFis'] = $modeloActFis->getAllActFisica();
        $data['num_comidas'] = self::NUMERO_COMIDAS_DIARIAS;
        $data['alergenos'] = $modeloAlergenos->getAll();
        return view('templates/left-menu.view.php').view('IMCform.view.php', $data).view('templates/footer.view.php');
    }

    function formEditResResult() {
        $errores = $this->checkForm($_POST);
        $input = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        $exito = true;
        $modeloInfoUsuarios = new \App\Models\InfoUsuariosModel();
        $modeloRelAlergenos = new \App\Models\RelAlergenosModel();
        $alergenos = $modeloRelAlergenos->getAlergenosUsuario($_SESSION['usuario']['id']);
        $infoUsuario = $modeloInfoUsuarios->getAllInfoUsuario($_SESSION['usuario']['id']);
        if (count($errores) == 0) {
            $alergenosNuevos = isset($_POST['alergenos']) ? $_POST['alergenos'] : [];
            if (empty($alergenosNuevos)) {
                $id_alergenos = $modeloRelAlergenos->getIdAlergenosUsuario($_SESSION['usuario']['id']);
                if (!is_null($id_alergenos)) {
                    foreach ($id_alergenos as $id) {
                        $exito = $modeloRelAlergenos->delete($id) ? true : false;
                    }
                }
            }
            $alergenosActualizar = !is_null($alergenos) ? array_diff($alergenosNuevos, $alergenos) : [];
            if (count($alergenosActualizar) > 0 || !empty($alergenosNuevos)) {
                $id_alergenos = $modeloRelAlergenos->getIdAlergenosUsuario($_SESSION['usuario']['id']);
                if (!is_null($id_alergenos)) {
                    foreach ($id_alergenos as $id) {
                        $exito = $modeloRelAlergenos->delete($id) ? true : false;
                    }
                }
                foreach ($alergenosNuevos as $alerNuevo) {
                    $exito = $modeloRelAlergenos->save(['id_usuario' => $_SESSION['usuario']['id'], 'alergeno' => $alerNuevo]) ? true : false;
                }
            }
            unset($_POST['alergenos']);
            $infoActualizar = array_diff_assoc($_POST, $infoUsuario[0]);
            $infoActualizar['id_usuario'] = $_SESSION['usuario']['id'];
            $reverse = array_reverse($infoActualizar);
            if (count($reverse) > 1) {
                if (isset($reverse['actividad_fisica']) || isset($reverse['objetivo'])) {
                    $imc = $this->getTMB($_POST);
                    $imc['id_usuario'] = $_SESSION['usuario']['id'];
                    $imcReverse = array_reverse($imc);
                    $exito = $modeloInfoUsuarios->save($imcReverse) ? true : false;
                }
                $exito = $modeloInfoUsuarios->save($reverse) ? true : false;
            }
            if ($exito) {
                $modeloSession = new \App\Models\SessionModel();
                $modeloAlergenos = new \App\Models\AlergenosModel();
                $_SESSION['usuario'] = $modeloSession->getAllUsuario($_SESSION['usuario']['id']);
                $alergenosUser = SessionController::getStringAlergenos($modeloAlergenos->getAlergenosUser($_SESSION['usuario']['id']));
                $_SESSION['usuario']['alergenos'] = $alergenosUser;
            }
            return $exito ? redirect()->to('/meal-plan')->with('exito', 'Datos actualizados con exito') : redirect()->to('/meal-plan')->with('error', 'Error al actualizar los datos');
        } else {
            $data['errores'] = $errores;
            $modeloDietas = new \App\Models\DietasModel();
            $modeloActFis = new \App\Models\ActFisicaModel();
            $modeloAlergenos = new \App\Models\AlergenosModel();
            $modeloRelAlergenos = new \App\Models\RelAlergenosModel();
            $modelo = new \App\Models\InfoUsuariosModel();
            $infoUsuario = $modelo->getAllInfoUsuario($_SESSION['usuario']['id']);
            $input['alergenos'] = $modeloRelAlergenos->getAlergenosUsuario($_SESSION['usuario']['id']);
            $data['input'] = $input;
            $data['metas'] = self::METAS;
            $data['editar'] = true;
            $data['generos'] = self::GENEROS;
            $data['dietas'] = $modeloDietas->getAllDietas();
            $data['actFis'] = $modeloActFis->getAllActFisica();
            $data['num_comidas'] = self::NUMERO_COMIDAS_DIARIAS;
            $data['alergenos'] = $modeloAlergenos->getAll();
            return view('IMCform.view.php', $data);
        }
    }

    function calcularImc(float $peso, float $altura): int {
        $alturaM = $altura / 100;
        return round(($peso / (pow($alturaM, 2))), 0);
    }

    function getTMB(array $datos): array {
        if (isset($datos['genero']) && $datos['genero'] == 'masculino') {
            $tbd = 88.362 + (13.397 * $datos['peso']) + (4.799 * $datos['estatura']) - (5.677 * $datos['edad']);
        }
        if (isset($datos['genero']) && $datos['genero'] == 'femenino') {
            $tbd = 447.593 + (9.247 * $datos['peso']) + (3.098 * $datos['estatura']) - (4.330 * $datos['edad']);
        }
        if ($datos['actividad_fisica'] == self::SEDENTARIO) {
            $factorActividad = self::FACTOR_ACTIVIDAD_SEDENTARIO;
        }
        if ($datos['actividad_fisica'] == self::MODERADO) {
            $factorActividad = self::FACTOR_ACTIVIDAD_MODERADA;
        }
        if ($datos['actividad_fisica'] == self::ACTIVA) {
            $factorActividad = self::FACTOR_ACTIVIDAD_ACTIVA;
        }
        if ($datos['actividad_fisica'] == self::MUY_ACTIVO) {
            $factorActividad = self::FACTOR_ACTIVIDAD_MUY_ACTIVO;
        }
        $caloriasDiarias = round($tbd * $factorActividad);
        if ($datos['objetivo'] == 'Perder Peso') {
            $caloriasObjetivo = round($caloriasDiarias - self::FACTOR_CALORICO);
        } else if ($datos['objetivo'] == 'Aumentar Masa Muscular') {
            $caloriasObjetivo = round($caloriasDiarias + self::FACTOR_CALORICO);
        } else {
            $caloriasObjetivo = round($caloriasDiarias);
        }
        return ([
            'metabolismo_basal' => (int) round($tbd),
            'calorias_mantenimiento' => (int) $caloriasDiarias,
            'calorias_objetivo' => (int) $caloriasObjetivo
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
        $modeloAlergenos = new \App\Models\AlergenosModel();
        $act_fisica = $modeloActividad->getAllIdActFisica();
        $dietas = $modeloDietas->getAllIdDietas();
        $alergenos = $modeloAlergenos->getAllIdAlergenos();
        if (empty($datos['nombre_completo'])) {
            $errores['nombre_completo'] = 'Este campo es obligatorio';
        } else {
            if (!preg_match('/[a-zA-Z\s]{6,}/', $datos['nombre_completo'])) {
                $errores['nombre_completo'] = 'solo puede estar formado por letras y espacios con una longitud minima de 6 caracteres';
            }
        }
        if (empty($datos['genero'])) {
            $errores['genero'] = 'este campo es obligatorio';
        } else {
            if (!in_array($datos['genero'], self::GENEROS)) {
                $errores['genero'] = 'Introduce un genero correcto';
            }
        }
        if (empty($datos['edad'])) {
            $errores['edad'] = 'el campo edad es obligatorio';
        } else {
            if (!is_numeric($datos['edad'])) {
                $errores['edad'] = 'tiene que ser un valor numerico';
            }
            if ($datos['edad'] < 12 || $datos['edad'] > 100) {
                $errores['edad'] = 'la edad tiene que estar comprendida entre 12 y 100 años';
            }
        }
        if (empty($datos['peso'])) {
            $errores['peso'] = 'el campo peso es obligatorio';
        } else {
            if (!is_numeric($datos['peso'])) {
                $errores['peso'] = 'tiene que ser un valor numerico';
            }
            if ($datos['peso'] < 20) {
                $errores['peso'] = 'introduzca un peso en KG valido';
            }
        }
        if (empty($datos['estatura'])) {
            $errores['estatura'] = 'la estatura es obligatoria';
        } else {
            if (!is_numeric($datos['estatura'])) {
                $errores['estatura'] = 'tiene que ser un valor numerico';
            }
            if ($datos['estatura'] <= 0) {
                $errores['estatura'] = 'introduzca una altura valida';
            }
        }
        if (empty($datos['actividad_fisica'])) {
            $errores['actividad_fisica'] = 'Este campo es obligatorio';
        } else {
            if (!in_array($datos['actividad_fisica'], $act_fisica)) {
                $errores['actividad_fisica'] = 'Introduce una actividad correcta';
            }
        }
        if (empty($datos['objetivo'])) {
            $errores['objetivo'] = 'este campo es obligatorio';
        } else {
            if (!in_array($datos['objetivo'], self::METAS)) {
                $errores['objetivo'] = 'tiene que seleccionar una opcion correcta';
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
            if (!in_array($datos['dieta'], $dietas)) {
                $errores['dieta'] = 'inserte una dieta correcta';
            }
        }
        if (!empty($datos['alergenos'])) {
            foreach ($datos['alergenos'] as $alergeno) {
                if (!in_array($alergeno, $alergenos)) {
                    $errores['alergenos'] = 'Introduce alergenos correctos';
                }
            }
        }
        if ($datos['num_comidas'] == 3) {
            if (empty($datos['porcent_breakfast'])) {
                $errores['porcent_breakfast'] = 'Introduce el porcentaje de las calorias totales que quiere consumir en el desayuno';
            } else {
                if (!is_numeric($datos['porcent_breakfast']) || ($datos['porcent_breakfast']) < 1 || $datos['porcent_breakfast'] > 100) {
                    $errores['porcent_brekfast'] = 'El porcentaje tiene que ser un valor numerico comprendido entre el 1 y el 100';
                }
            }
            if (empty($datos['porcent_lunch'])) {
                $errores['porcent_lunch'] = 'Introduce el porcentaje de las calorias totales que quiere consumir en la comida';
            } else {
                if (!is_numeric($datos['porcent_lunch']) || ($datos['porcent_lunch']) < 1 || $datos['porcent_lunch'] > 100) {
                    $errores['porcent_lunch'] = 'El porcentaje tiene que ser un valor numerico comprendido entre el 1 y el 100';
                }
            }
            if (empty($datos['porcent_dinner'])) {
                $errores['porcent_dinner'] = 'Introduce el porcentaje de las calorias totales que quiere consumir en la cena';
            } else {
                if (!is_numeric($datos['porcent_dinner']) || ($datos['porcent_dinner']) < 1 || $datos['porcent_dinner'] > 100) {
                    $errores['porcent_dinner'] = 'El porcentaje tiene que ser un valor numerico comprendido entre el 1 y el 100';
                }
            }
            if ($datos['porcent_breakfast'] + $datos['porcent_lunch'] + $datos['porcent_dinner'] !== 100) {
                $errores['porcent_dinner'] = 'la suma de todos los porcentajes de las comidas tiene que ser el 100%';
            }
        }
        if ($datos['num_comidas'] == 4) {
            if (empty($datos['porcent_breakfast'])) {
                $errores['porcent_breakfast'] = 'Introduce el porcentaje de las calorias totales que quiere consumir en el desayuno';
            } else {
                if (!is_numeric($datos['porcent_breakfast']) || ($datos['porcent_breakfast']) < 1 || $datos['porcent_breakfast'] > 100) {
                    $errores['porcent_brekfast'] = 'El porcentaje tiene que ser un valor numerico comprendido entre el 1 y el 100';
                }
            }
            if (empty($datos['porcent_brunch'])) {
                $errores['porcent_brunch'] = 'Introduce el porcentaje de las calorias totales que quiere consumir en el brunch';
            } else {
                if (!is_numeric($datos['porcent_brunch']) || ($datos['porcent_brunch']) < 1 || $datos['porcent_brunch'] > 100) {
                    $errores['porcent_brekfast'] = 'El porcentaje tiene que ser un valor numerico comprendido entre el 1 y el 100';
                }
            }
            if (empty($datos['porcent_lunch'])) {
                $errores['porcent_lunch'] = 'Introduce el porcentaje de las calorias totales que quiere consumir en la comida';
            } else {
                if (!is_numeric($datos['porcent_lunch']) || ($datos['porcent_lunch']) < 1 || $datos['porcent_lunch'] > 100) {
                    $errores['porcent_lunch'] = 'El porcentaje tiene que ser un valor numerico comprendido entre el 1 y el 100';
                }
            }
            if (empty($datos['porcent_dinner'])) {
                $errores['porcent_dinner'] = 'Introduce el porcentaje de las calorias totales que quiere consumir en la cena';
            } else {
                if (!is_numeric($datos['porcent_dinner']) || ($datos['porcent_dinner']) < 1 || $datos['porcent_dinner'] > 100) {
                    $errores['porcent_dinner'] = 'El porcentaje tiene que ser un valor numerico comprendido entre el 1 y el 100';
                }
            }
            if ($datos['porcent_breakfast'] + $datos['porcent_lunch'] + $datos['porcent_dinner'] + $datos['porcent_brunch'] !== 100) {
                $errores['porcent_dinner'] = 'la suma de todos los porcentajes de las comidas tiene que ser el 100%';
            }
        }
        if ($datos['num_comidas'] == 5) {
            if (empty($datos['porcent_breakfast'])) {
                $errores['porcent_breakfast'] = 'Introduce el porcentaje de las calorias totales que quiere consumir en el desayuno';
            } else {
                if (!is_numeric($datos['porcent_breakfast']) || ($datos['porcent_breakfast']) < 1 || $datos['porcent_breakfast'] > 100) {
                    $errores['porcent_brekfast'] = 'El porcentaje tiene que ser un valor numerico comprendido entre el 1 y el 100';
                }
            }
            if (empty($datos['porcent_brunch'])) {
                $errores['porcent_breakfast'] = 'Introduce el porcentaje de las calorias totales que quiere consumir en el desayuno';
            } else {
                if (!is_numeric($datos['porcent_brunch']) || ($datos['porcent_brunch']) < 1 || $datos['porcent_brunch'] > 100) {
                    $errores['porcent_brekfast'] = 'El porcentaje tiene que ser un valor numerico comprendido entre el 1 y el 100';
                }
            }
            if (empty($datos['porcent_lunch'])) {
                $errores['porcent_lunch'] = 'Introduce el porcentaje de las calorias totales que quiere consumir en la comida';
            } else {
                if (!is_numeric($datos['porcent_lunch']) || ($datos['porcent_lunch']) < 1 || $datos['porcent_lunch'] > 100) {
                    $errores['porcent_lunch'] = 'El porcentaje tiene que ser un valor numerico comprendido entre el 1 y el 100';
                }
            }
            if (empty($datos['porcent_snack'])) {
                $errores['porcent_snack'] = 'Introduce el porcentaje de las calorias totales que quiere consumir en el snack';
            } else {
                if (!is_numeric($datos['porcent_snack']) || ($datos['porcent_snack']) < 1 || $datos['porcent_snack'] > 100) {
                    $errores['porcent_lunch'] = 'El porcentaje tiene que ser un valor numerico comprendido entre el 1 y el 100';
                }
            }
            if (empty($datos['porcent_dinner'])) {
                $errores['porcent_dinner'] = 'Introduce el porcentaje de las calorias totales que quiere consumir en la cena';
            } else {
                if (!is_numeric($datos['porcent_dinner']) || ($datos['porcent_dinner']) < 1 || $datos['porcent_dinner'] > 100) {
                    $errores['porcent_dinner'] = 'El porcentaje tiene que ser un valor numerico comprendido entre el 1 y el 100';
                }
            }
            if ($datos['porcent_breakfast'] + $datos['porcent_lunch'] + $datos['porcent_dinner'] + $datos['porcent_brunch'] + $datos['porcent_snack'] !== 100) {
                $errores['porcent_dinner'] = 'la suma de todos los porcentajes de las comidas tiene que ser el 100%';
            }
        }
        return $errores;
    }

}
