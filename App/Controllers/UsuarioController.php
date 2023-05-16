<?php

namespace App\Controllers;

class UsuarioController extends \App\Core\BaseController {

    function showData(){
        $data = [];
        $modelo = new \App\Models\HistorialPesoModel();
        $data['pesos'] = $modelo->getPesosUsuario($_SESSION['usuario']['id']);
        $fechas = [];
        $pesos = [];
        if($data['pesos']!= null){
            foreach ($data['pesos'] as $p) {
                array_push($fechas, $p['fecha']);
                array_push($pesos, $p['peso']);
            }
            $data['fechas'] = $fechas;
            $data['pesos_chart'] = $pesos;
            
        }else{
            $data['fechas']=$fechas;
            $data['pesos_chart']=$pesos;
        }
        return $data;
    }
    function showMealPlan(){
        
    }
    
    function showAccount() {
        $data = $this->showData();
        $this->view->showViews(array('left-menu.view.php', 'account-details.view.php'), $data);
    }

    function addPeso() {
        $errores = $this->checkForm($_POST);
        if (count($errores) == 0) {
            $modelo = new \App\Models\HistorialPesoModel();
            if ($modelo->addNewPeso($_SESSION['usuario']['id'], $_POST['peso'], $_POST['fecha'])) {
                return redirect()->to('/account');
            }
        } else {
            $data= $this->showData();
            $data['errores'] = $errores;
            return view('left-menu.view.php') . view('account-details.view.php', $data);
        }
    }

    function deletePeso(int $id) {
        $modelo = new \App\Models\HistorialPesoModel();
        if (!$modelo->deletePeso($id)) {
            $data['error'] = 'no se ha podido eliminar el peso, intentelo de nuevo';
        }
        return redirect()->to('/account');
    }


    function checkForm(array $datos, bool $edit = false, int $id = 0): array {
        $errores = [];
        $modelo = new \App\Models\HistorialPesoModel();
        if ($edit==true) {
            if (empty($datos['fecha'])) {
                $errores['fecha'] = 'para editar tiene que introducir una fecha';
            } else {
                if ($modelo->existePesoDiaEdit($_SESSION['usuario']['id'], $id, $datos['fecha'])) {
                    $errores['fecha'] = 'no puede modificar a esa fecha, porque ya esta ocupada por otro peso';
                }
                if (empty($datos['peso'])) {
                    $errores['peso'] = 'tiene que introducir un peso en kg';
                } else {
                    if (!is_numeric($datos['peso']) || $datos['peso'] < 10) {
                        $errores['peso'] = 'el peso tiene que ser un numero mayor que 10';
                    }
                }
            }
        } else {
            if (empty($datos['fecha'])) {
                $errores['fecha'] = 'tiene que introducir una fecha';
            } else {
                if ($modelo->existePesoDia($_SESSION['usuario']['id'], $datos['fecha'])) {
                    $errores['fecha'] = 'no puede introducir 2 pesos el mismo dia';
                }
//            $patron = '~(0[1-9]|1[012])[-/](0[1-9]|[12][0-9]|3[01])[-/](19|20)\d\d~';
//            if (!preg_match($patron, $datos['fecha'])) {
//                $errores['fecha'] = 'introduce una fecha valida';
//            }
            }
            if (empty($datos['peso'])) {
                $errores['peso'] = 'tiene que introducir un peso en kg';
            } else {
                if (!is_numeric($datos['peso']) || $datos['peso'] < 11) {
                    $errores['peso'] = 'el peso tiene que ser un numero mayor que 10';
                }
            }
        }
        return $errores;
    }

}
