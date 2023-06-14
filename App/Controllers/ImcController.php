<?php

namespace App\Controllers;

use App\Controllers\SessionController;

class ImcController extends \App\Core\BaseController {

    const GENEROS = ['male', 'female'];
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
    const METAS = ['Lose Weigth', 'Maintain Weigth', 'Increase muscle mass'];

    function showFormIMC() {
        $modeloDietas = new \App\Models\DietasModel();
        $modeloActFis = new \App\Models\ActFisicaModel();
        $modeloAlergenos = new \App\Models\AlergenosModel();
        $data['metas'] = self::METAS;
        $data['generos'] = self::GENEROS;
        $data['editar'] = false;
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
        $input = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        if (count($errores) == 0) {
            $imc = $this->calcularImc($_POST['peso'], $_POST['estatura']);
            $calorias = $this->getTMB($_POST);
            $alergenos = empty($_POST['alergenos']) ? '' : $_POST['alergenos'];
            unset($_POST['alergenos']);
            $modeloRelAlergenos = new \App\Models\RelAlergenosModel();
            $add = array_merge($_POST, $calorias);
            $add['imc'] = $imc;
            $add['id_usuario'] = $_SESSION['usuario']['id'];
            $addReverse = array_reverse($add);
            if ($modeloInfoUsuarios->save($addReverse)) {
                $infoUsuario = $modeloSesion->getAllUsuario($_SESSION['usuario']['id']);
                $this->session->set('usuario', $infoUsuario);
                if (!empty($alergenos) && is_array($alergenos)) {
                    foreach ($alergenos as $alergeno) {
                        $modeloRelAlergenos->save(['id_usuario' => $_SESSION['usuario']['id'], 'alergeno' => $alergeno]);
                    }
                    $_SESSION['usuario']['alergenos'] = $modeloRelAlergenos->getAlergenosUsuario($_SESSION['usuario']['id']);
                }
                return redirect()->to('/meal-plan');
            }
            return redirect()->to('/imc')->with('error', 'An error occurred while saving the form data.');
        } else {
            $data['errores'] = $errores;
            $data['input'] = $input;
            $modeloDietas = new \App\Models\DietasModel();
            $modeloActFis = new \App\Models\ActFisicaModel();
            $modeloAlergenos = new \App\Models\AlergenosModel();
            $data['metas'] = self::METAS;
            $data['generos'] = self::GENEROS;
            $data['editar'] = false;
            $data['dietas'] = $modeloDietas->getAllDietas();
            $data['actFis'] = $modeloActFis->getAllActFisica();
            $data['num_comidas'] = self::NUMERO_COMIDAS_DIARIAS;
            $data['alergenos'] = $modeloAlergenos->getAll();
            return view('IMCform.view.php', $data);
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
        return view('templates/left-menu.view.php') . view('IMCform.view.php', $data) . view('templates/footer.view.php');
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
            return $exito ? redirect()->to('/meal-plan')->with('exito', 'Data updated successfully.') : redirect()->to('/meal-plan')->with('error', 'Error while updating the data.');
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
            return view('templates/left-menu.view.php') . view('IMCform.view.php', $data) . view('templates/footer.view.php');
        }
    }

    function calcularImc(float $peso, float $altura): int {
        $alturaM = $altura / 100;
        return round(($peso / (pow($alturaM, 2))), 0);
    }

    function getTMB(array $datos): array {
        if (isset($datos['genero']) && $datos['genero'] == 'male') {
            $tbd = 88.362 + (13.397 * $datos['peso']) + (4.799 * $datos['estatura']) - (5.677 * $datos['edad']);
        }
        if (isset($datos['genero']) && $datos['genero'] == 'female') {
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

    function checkForm(array $datos): array {
        $errores = [];
        $modeloActividad = new \App\Models\ActFisicaModel();
        $modeloDietas = new \App\Models\DietasModel();
        $modeloAlergenos = new \App\Models\AlergenosModel();
        $act_fisica = $modeloActividad->getAllIdActFisica();
        $dietas = $modeloDietas->getAllIdDietas();
        $alergenos = $modeloAlergenos->getAllIdAlergenos();
        if (empty($datos['nombre_completo'])) {
            $errores['nombre_completo'] = 'This field is required.';
        } else {
            if (!preg_match('/[a-zA-Z\s]{6,}/', $datos['nombre_completo'])) {
                $errores['nombre_completo'] = 'It can only consist of letters and spaces, with a minimum length of 6 characters.';
            }
        }
        if (empty($datos['genero'])) {
            $errores['genero'] = 'This field is required.';
        } else {
            if (!in_array($datos['genero'], self::GENEROS)) {
                $errores['genero'] = 'Introduce a correct gender';
            }
        }
        if (empty($datos['edad'])) {
            $errores['edad'] = 'This field is required.';
        } else {
            if (!is_numeric($datos['edad'])) {
                $errores['edad'] = 'It must be a numerical value.';
            }
            if ($datos['edad'] < 12 || $datos['edad'] > 100) {
                $errores['edad'] = 'The age must be between 12 and 100 years.';
            }
        }
        if (empty($datos['peso'])) {
            $errores['peso'] = 'This field is required';
        } else {
            if (!is_numeric($datos['peso'])) {
                $errores['peso'] = 'It must be a numeric value';
            }
            if ($datos['peso'] < 20) {
                $errores['peso'] = 'Please enter a valid weight.';
            }
        }
        if (empty($datos['estatura'])) {
            $errores['estatura'] = 'This fiel is required';
        } else {
            if (!is_numeric($datos['estatura'])) {
                $errores['estatura'] = 'It must be a numeric value';
            }
            if ($datos['estatura'] <= 0) {
                $errores['estatura'] = 'Please enter a valid height.';
            }
        }
        if (empty($datos['actividad_fisica'])) {
            $errores['actividad_fisica'] = 'This field is required';
        } else {
            if (!in_array($datos['actividad_fisica'], $act_fisica)) {
                $errores['actividad_fisica'] = 'Please enter a valid activity.';
            }
        }
        if (empty($datos['objetivo'])) {
            $errores['objetivo'] = 'This field is required';
        } else {
            if (!in_array($datos['objetivo'], self::METAS)) {
                $errores['objetivo'] = 'You must select a valid option.';
            }
        }
        if (empty($datos['num_comidas'])) {
            $errores['num_comidas'] = 'Please enter the number of meals you want to have per day.';
        } else {
            if (!in_array($datos['num_comidas'], self::NUMERO_COMIDAS_DIARIAS)) {
                $errores['num_comidas'] = 'The value of meals is not correct';
            }
        }
        if (empty($datos['dieta'])) {
            $errores['dieta'] = 'Please enter the diet you want to follow.';
        } else {
            if (!in_array($datos['dieta'], $dietas)) {
                $errores['dieta'] = 'Insert a value diet';
            }
        }
        if (!empty($datos['alergenos'])) {
            foreach ($datos['alergenos'] as $alergeno) {
                if (!in_array($alergeno, $alergenos)) {
                    $errores['alergenos'] = 'Introduce a value allergen';
                }
            }
        }
        if ($datos['num_comidas'] == 3) {
            if (empty($datos['porcent_breakfast'])) {
                $errores['porcent_breakfast'] = 'Please enter the percentage of total calories you want to consume for breakfast.';
            } else {
                if (!is_numeric($datos['porcent_breakfast']) || ($datos['porcent_breakfast']) < 1 || $datos['porcent_breakfast'] > 100) {
                    $errores['porcent_brekfast'] = 'The percentage must be a numerical value between 1 and 100.';
                }
            }
            if (empty($datos['porcent_lunch'])) {
                $errores['porcent_lunch'] = 'Please enter the percentage of total calories you want to consume for lunch.';
            } else {
                if (!is_numeric($datos['porcent_lunch']) || ($datos['porcent_lunch']) < 1 || $datos['porcent_lunch'] > 100) {
                    $errores['porcent_lunch'] = 'The percentage must be a numerical value between 1 and 100.';
                }
            }
            if (empty($datos['porcent_dinner'])) {
                $errores['porcent_dinner'] = 'Please enter the percentage of total calories you want to consume for dinner.';
            } else {
                if (!is_numeric($datos['porcent_dinner']) || ($datos['porcent_dinner']) < 1 || $datos['porcent_dinner'] > 100) {
                    $errores['porcent_dinner'] = 'The percentage must be a numerical value between 1 and 100.';
                }
            }
            if ($datos['porcent_breakfast'] + $datos['porcent_lunch'] + $datos['porcent_dinner'] !== 100) {
                $errores['porcent_dinner'] = 'The sum of all meal percentages must be 100%';
            }
        }
        if ($datos['num_comidas'] == 4) {
            if (empty($datos['porcent_breakfast'])) {
                $errores['porcent_breakfast'] = 'Please enter the percentage of total calories you want to consume for breakfast.';
            } else {
                if (!is_numeric($datos['porcent_breakfast']) || ($datos['porcent_breakfast']) < 1 || $datos['porcent_breakfast'] > 100) {
                    $errores['porcent_brekfast'] = 'The percentage must be a numerical value between 1 and 100.';
                }
            }
            if (empty($datos['porcent_brunch'])) {
                $errores['porcent_brunch'] = 'Please enter the percentage of total calories you want to consume for brunch.';
            } else {
                if (!is_numeric($datos['porcent_brunch']) || ($datos['porcent_brunch']) < 1 || $datos['porcent_brunch'] > 100) {
                    $errores['porcent_brekfast'] = 'The percentage must be a numerical value between 1 and 100.';
                }
            }
            if (empty($datos['porcent_lunch'])) {
                $errores['porcent_lunch'] = 'Please enter the percentage of total calories you want to consume for lunch.';
            } else {
                if (!is_numeric($datos['porcent_lunch']) || ($datos['porcent_lunch']) < 1 || $datos['porcent_lunch'] > 100) {
                    $errores['porcent_lunch'] = 'The percentage must be a numerical value between 1 and 100.';
                }
            }
            if (empty($datos['porcent_dinner'])) {
                $errores['porcent_dinner'] = 'Please enter the percentage of total calories you want to consume for dinner.';
            } else {
                if (!is_numeric($datos['porcent_dinner']) || ($datos['porcent_dinner']) < 1 || $datos['porcent_dinner'] > 100) {
                    $errores['porcent_dinner'] = 'The percentage must be a numerical value between 1 and 100.';
                }
            }
            if ($datos['porcent_breakfast'] + $datos['porcent_lunch'] + $datos['porcent_dinner'] + $datos['porcent_brunch'] !== 100) {
                $errores['porcent_dinner'] = 'The sum of all meal percentages must be 100%';
            }
        }
        if ($datos['num_comidas'] == 5) {
            if (empty($datos['porcent_breakfast'])) {
                $errores['porcent_breakfast'] = 'Please enter the percentage of total calories you want to consume for breakfast.';
            } else {
                if (!is_numeric($datos['porcent_breakfast']) || ($datos['porcent_breakfast']) < 1 || $datos['porcent_breakfast'] > 100) {
                    $errores['porcent_brekfast'] = 'The percentage must be a numerical value between 1 and 100.';
                }
            }
            if (empty($datos['porcent_brunch'])) {
                $errores['porcent_brunch'] = 'Please enter the percentage of total calories you want to consume for brunch.';
            } else {
                if (!is_numeric($datos['porcent_brunch']) || ($datos['porcent_brunch']) < 1 || $datos['porcent_brunch'] > 100) {
                    $errores['porcent_brunch'] = 'The percentage must be a numerical value between 1 and 100.';
                }
            }
            if (empty($datos['porcent_lunch'])) {
                $errores['porcent_lunch'] = 'Please enter the percentage of total calories you want to consume for lunch.';
            } else {
                if (!is_numeric($datos['porcent_lunch']) || ($datos['porcent_lunch']) < 1 || $datos['porcent_lunch'] > 100) {
                    $errores['porcent_lunch'] = 'The percentage must be a numerical value between 1 and 100.';
                }
            }
            if (empty($datos['porcent_snack'])) {
                $errores['porcent_snack'] = 'Please enter the percentage of total calories you want to consume for snack.';
            } else {
                if (!is_numeric($datos['porcent_snack']) || ($datos['porcent_snack']) < 1 || $datos['porcent_snack'] > 100) {
                    $errores['porcent_lunch'] = 'The percentage must be a numerical value between 1 and 100.';
                }
            }
            if (empty($datos['porcent_dinner'])) {
                $errores['porcent_dinner'] = 'Please enter the percentage of total calories you want to consume for dinner.';
            } else {
                if (!is_numeric($datos['porcent_dinner']) || ($datos['porcent_dinner']) < 1 || $datos['porcent_dinner'] > 100) {
                    $errores['porcent_dinner'] = 'The percentage must be a numerical value between 1 and 100.';
                }
            }
            if ($datos['porcent_breakfast'] + $datos['porcent_lunch'] + $datos['porcent_dinner'] + $datos['porcent_brunch'] + $datos['porcent_snack'] !== 100) {
                $errores['porcent_dinner'] = 'The sum of all meal percentages must be 100%';
            }
        }
        return $errores;
    }

}
