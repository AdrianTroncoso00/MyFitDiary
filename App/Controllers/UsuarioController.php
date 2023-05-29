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
    
    function changeUsername(){
        $modeloSesion = new \App\Models\SessionModel();
        $errores = $this->checkUsername($_POST);
        if(count($errores)==0){
            if(!$modeloSesion->changeUsername($_POST['username'], $_SESSION['usuario']['id'])){
                
            }else{
                return redirect()->to('/account');
            }
        }
    }

    function changePassword(){
        $modeloSesion = new \App\Models\SessionModel();
        $errores = $this->checkPass($_POST);
        if(count($errores)==0){
            if(!$modeloSesion->changePass($_POST['pass'], $_SESSION['usuario']['id'])){
                
            }else{
                return redirect()->to('/account');
            }
        }
    }
    
    function checkUsername(array $datos):array{
        $errores = [];
        if(!empty($datos['username'])){
            if(!preg_match('/[0-9a-zA-Z_]{8,}/', $datos['pass'])){
                $errores['username']='el username tiene que tener 8 caracteres minimo y solo puede estar formado por letras, numeros y  _';
            }
        }else{
            $errores['username']='introduce una contraseña';
        }
        return $errores; 
    }
    
    function checkPass(array $datos):array{
        $errores = [];
        if(!empty($datos['pass'])){
            if(!preg_match('/[0-9a-zA-Z_]{8,}/', $datos['pass'])){
                $errores['pass']='la contraseña tiene que tener 8 caracteres minimo y solo puede estar formado por letras, numeros y  _';
            }
        }else{
            $errores['pass']='introduce una contraseña';
        }
        return $errores;
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
