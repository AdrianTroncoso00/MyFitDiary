<?php

namespace App\Controllers;

use App\Controllers\EdamamController;

class SessionController extends \App\Core\BaseController {

    public function showLogIn() {
        return view('login.view.php');
    }

    public function showSignUp() {
        return view('signup.view.php');
    }
    
    function pruebaVista(){
        return view('templates/left-menu.view.php');
    }
    function LogInProcess() {
        $data = [];
        $semana = EdamamController::getSemanaActual();
        $modelo = new \App\Models\SessionModel();
        $modeloAlergenos= new \App\Models\AlergenosModel();
        $modeloComidas = new \App\Models\ComidasModel();
        $usuario = $modelo->login($_POST['email'], $_POST['pass']);
        if (!is_null($usuario)) {
            $this->session->set('usuario',$usuario);
            var_dump($_SESSION);
            $alergenos = $modeloAlergenos->getAlergenosUser($_SESSION['usuario']['id']);
            !is_null($alergenos) ? $_SESSION['usuario']['alergenos']= $this->getStringAlergenos($alergenos) : $_SESSION['usuario']['alergenos']=null;
            $modeloComidas->deleteComidasSemanaAnterior($_SESSION['usuario']['id'], $semana[0]);
            $modelo->save(['id'=>$_SESSION['usuario']['id'], 'last_login'=>date("Y-m-d")]);
            return redirect()->to('/meal-plan');
        } else {
            $data['input'] = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            $data['error'] = 'The entered data is incorrect.';
            return view('login.view.php', $data);
        }
    }
    
    function getStringAlergenos(array $alergenos):array{
        $string=[];
        foreach ($alergenos as $alergeno) {
            array_push($string,$alergeno['nombre_alergeno']);
        }
        return $string;
    }

    function signUp() {
        $data = [];
        $errores = $this->checkForm($_POST, true);
        $input = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        if (count($errores) == 0) {
            $modelo = new \App\Models\SessionModel();
            $addUsuario=([
                'username'=>$_POST['username'],
                'email'=>$_POST['email'],
                'pass'=> password_hash($_POST['pass'], PASSWORD_DEFAULT),
                'rol'=>'standart'
            ]);
            $exito = $modelo->save($addUsuario);
            if ($exito) {
                $user = $modelo->getUser($_POST['email']);
                $this->session->set('usuario', $user);
                return redirect()->to('/imc');
            } else {
                return redirect()->to('/signup')->with('error','The user could not be added.');
            }
        } else {
            $data['input'] = $input;
            $data['errores'] = $errores;
            return view('signup.view.php', $data);
        }
    }

    function logOut() {
        session_destroy();
        return redirect()->to('/login');
    }

    function checkForm(array $datos, bool $alta = false): array {
        $errores = [];
        $modelo = new \App\Models\SessionModel();
        if (empty($datos['email'])) {
            $errores['email'] = 'Please enter an email.';
        } else {
            if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                $errores['email'] = 'Please enter an email valid.';
            }
        }
        if ($alta || !empty($datos['pass2'])) {
            if (empty($datos['email'])) {
                $errores['email'] = 'Please enter an email.';
            } else {
                if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                    $errores['email'] = 'Please enter an email valid.';
                } else {

                    if ($modelo->existeParam('email',$datos['email'])) {
                        $errores['email'] = 'This email is already in use.';
                    }
                }
            }
            if (empty($datos['username'])) {
                $errores ['username'] = 'Please enter a username.';
            } else {
                if (!preg_match('/[0-9a-zA-Z_]{1,}/', $datos['username'])) {
                    $errores['username'] = 'The username can only consist of letters, numbers, and underscores.';
                }
            }
            if (empty($datos['pass'])) {
                $errores['pass'] = 'The password is required';
            } else {
                if (!preg_match('/[0-9a-zA-Z_]{8,}/', $datos['pass'])) {
                    $errores['username'] = 'The password can only consist of letters, numbers, and underscores, and must be a minimum of 8 characters.';
                }
                if ($modelo->existeParam('username', $datos['username'])) {
                    $errores['username'] = 'The username is already in use.';
                }
            }
            if ($datos['pass'] !== $datos['pass2']) {
                $errores['pass'] = 'The passwords must be the same.';
            }
            if (empty($datos['pass']) || empty($datos['pass2'])) {
                $errores['pass'] = 'You need to enter both passwords.';
            }
        }
        return $errores;
    }

}
