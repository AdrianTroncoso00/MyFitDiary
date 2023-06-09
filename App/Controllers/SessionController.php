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

    function LogInProcess() {
        $data = [];
        $semana = EdamamController::getSemanaActual();
        $modelo = new \App\Models\SessionModel();
        $modeloAlergenos= new \App\Models\AlergenosModel();
        $modeloComidas = new \App\Models\ComidasModel();
        $usuario = $modelo->login($_POST['email'], $_POST['pass']);
        if (!is_null($usuario)) {
            $this->session->set('usuario',$usuario);
            $alergenos = $modeloAlergenos->getAlergenosUser($_SESSION['usuario']['id']);
            !is_null($alergenos) ? $_SESSION['usuario']['alergenos']= $this->getStringAlergenos($alergenos) : $_SESSION['usuario']['alergenos']=null;
            $modeloComidas->deleteComidasSemanaAnterior($_SESSION['usuario']['id'], $semana[0]);
            $modelo->save(['id'=>$_SESSION['usuario']['id'], 'last_login'=>date("Y-m-d")]);
            return redirect()->to('/meal-plan');
        } else {
            $data['input'] = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            $data['error'] = 'Los datos introducidos no son correctos';
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
                return redirect()->to('/signup')->with('error','No se ha podido añadir el usuario');
            }
        } else {
            $data['input'] = $input;
            $data['errores'] = $errores;
            return view('signup.view.php', $data);
        }
    }

    function logOut() {
        session_destroy();
        return view('login.view.php');
    }

    function checkForm(array $datos, bool $alta = false): array {
        $errores = [];
        $modelo = new \App\Models\SessionModel();
        if (empty($datos['email'])) {
            $errores['email'] = 'Introduzca un email';
        } else {
            if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                $errores['email'] = 'introduzca un email valido';
            }
        }
        if ($alta || !empty($datos['pass2'])) {
            if (empty($datos['email'])) {
                $errores['email'] = 'Introduzca un email';
            } else {
                if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                    $errores['email'] = 'introduzca un email valido';
                } else {

                    if ($modelo->existeParam('email',$datos['email'])) {
                        $errores['email'] = 'este email ya se encuentra en uso';
                    }
                }
            }
            if (empty($datos['username'])) {
                $errores ['username'] = 'introduzca un nombre de usuario';
            } else {
                if (!preg_match('/[0-9a-zA-Z_]{1,}/', $datos['username'])) {
                    $errores['username'] = 'el nombre de usuariosolo puede estar formado por letras, numeros y guiones bajos';
                }
            }
            if (empty($datos['pass'])) {
                $errores['pass'] = 'La contraseña es obligatoria';
            } else {
                if (!preg_match('/[0-9a-zA-Z_]{8,}/', $datos['pass'])) {
                    $errores['username'] = 'la contraseña tiene solo puede estar formada por letras, numeros y guiones bajos y minimo'
                            . '8 caracteres';
                }
                if ($modelo->existeParam('username', $datos['username'])) {
                    $errores['username'] = 'el nombre de usuario ya se encuentra en uso';
                }
            }
            if ($datos['pass'] !== $datos['pass2']) {
                $errores['pass'] = 'las contraseñas tienen que ser iguales';
            }
            if (empty($datos['pass']) || empty($datos['pass2'])) {
                $errores['pass'] = 'tienes que introducir las 2 contraseñas';
            }
        }
        return $errores;
    }

}
