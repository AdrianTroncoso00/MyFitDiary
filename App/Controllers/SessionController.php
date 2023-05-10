<?php

namespace App\Controllers;

class SessionController extends \App\Core\BaseController {
    
    const NUM_COMIDAS_DIARIAS = [3,4,5];
    public function showLogIn() {
        return view('login.view.php');
    }

    public function showSignUp() {
        return view('signup.view.php');
    }

    public function showRecCont() {
        return view('recuperarContrasena.view.php');
    }

    function LogInProcess() {
        $data = [];
        $modelo = new \App\Models\SessionModel();
        $usuario = $modelo->login($_POST['email'], $_POST['pass']);
        if (!is_null($usuario)) {
            $_SESSION['usuario'] = $usuario;
            var_dump($_SESSION['usuario']);
            $modelo->updateLastDate($_SESSION['usuario']['id']);
            $data['etiquetas'] = ['Proteinas', 'Grasas', 'Carbohidratos'];
            $data['valores_etiquetas'] = [200, 10, 70];
            $data['chart_colors'] = [
                'rgb(255, 99, 132)',
                'rgb(54, 162, 235)',
                'rgb(255, 205, 86)'];
            //return view('left-menu.view.php') . view('meal-plan.view.php', $data);
            return redirect()->to('/meal-plan');
        } else {
            $data['input'] = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            $data['error'] = 'Los datos introducidos no son correctos';
            return view('login.view.php', $data);
        }
    }

    function signUp() {
        $data = [];
        $errores = $this->checkForm($_POST, true);
        $input = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        if (count($errores) == 0) {
            $modelo = new \App\Models\SessionModel();
            $exito = $modelo->signUp($_POST);
            if ($exito) {
                $user = $modelo->login($_POST['email'], $_POST['pass']);
                if (!is_null($user)) {
                    $_SESSION['usuario'] = $user;
                    $modeloDietas = new \App\Models\DietasModel();
                    $data['dietas']=$modeloDietas->getAllDietas();
                    $modeloActFis= new \App\Models\ActFisicaModel();
                    $data['actFis']=$modeloActFis->getAllActFisica();
                    $data['num_comidas']= self::NUM_COMIDAS_DIARIAS;
                    return view('IMCform.view.php', $data);
                }
            } else {
                $data['input'] = $input;
                $data['errores']['error'] = 'error indeterminado al guardar';
                return view('signup.view.php', $data);
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
    
    function changePass(){
        $modelo = new \App\Models\SessionModel();
        $id=$modelo->getIdByEmail($_POST['email']);
        if($id>0){
            if($modelo->changePass($_POST['pass'], $id)){
                return view('login.view.php');
            }
        }
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

                    if ($modelo->existeParametro('email', $datos['email'])) {
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
                if ($modelo->existeParametro('username', $datos['username'])) {
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
