<?php

namespace App\Controllers;

class UsuarioController extends \App\Core\BaseController {

    function showData() {
        $data = [];
        $modelo = new \App\Models\HistorialPesoModel();
        $data['pesos'] = $modelo->getPesosUsuario($_SESSION['usuario']['id']);
        $fechas = [];
        $pesos = [];
        if ($data['pesos'] != null) {
            foreach ($data['pesos'] as $p) {
                array_push($fechas, $p['fecha']);
                array_push($pesos, $p['peso']);
            }
            $data['fechas'] = $fechas;
            $data['pesos_chart'] = $pesos;
        } else {
            $data['fechas'] = $fechas;
            $data['pesos_chart'] = $pesos;
        }
        return $data;
    }

    function showFormChangePass() {
        $data['titulo'] = 'Change Pass';
        $data['label'] = 'Password';
        $data['name'] = 'pass';
        return view('templates/left-menu.view.php') . view('settings.view.php', $data) . view('templates/footer.view.php');
    }

    function showFormChangeUsername() {
        $data['titulo'] = 'Change Username';
        $data['label'] = 'Username';
        $data['name'] = 'user';
        return view('templates/left-menu.view.php') . view('settings.view.php', $data) . view('templates/footer.view.php');
    }

    function showAccount() {
        $data = $this->showData();
        return view('templates/left-menu.view.php') . view('account-details.view.php', $data) . view('templates/footer.view.php');
    }

    function addPeso() {
        $errores = $this->checkForm($_POST);
        var_dump($_POST);
        $modeloInfoUsuarios = new \App\Models\InfoUsuariosModel();
        if (count($errores) == 0) {
            $modelo = new \App\Models\HistorialPesoModel();
            if ($modelo->save(['id_usuario' => $_SESSION['usuario']['id'], 'peso' => $_POST['peso'], 'fecha' => $_POST['fecha']])) {
                return $modeloInfoUsuarios->save(['id_usuario' => $_SESSION['usuario']['id'], 'peso' => $_POST['peso']]) ? redirect()->to('/account')->with('exito', 'Peso añadido correctamente') : redirect()->to('/account')->with('error', 'No se ha podido añadir el peso');
            }
            return redirect()->to('/account')->with('error', 'No se ha podido añadir el peso');
        } else {
            $data = $this->showData();
            $data['errores'] = $errores;
            $data['input'] = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            return view('templates/left-menu.view.php') . view('account-details.view.php', $data) . view('templates/footer.view.php');
        }
    }

    function deletePeso(int $id) {
        $modelo = new \App\Models\HistorialPesoModel();
        if (!$modelo->delete($id)) {
            return redirect()->to('/account')->with('error', 'no se ha podido eliminar el peso, intentelo de nuevo');
        }
        return redirect()->to('/account')->with('exito', 'Peso eliminado con exito');
    }

    function changeUsername() {
        $modeloSesion = new \App\Models\SessionModel();
        $errores = $this->checkUsername($_POST, $_SESSION['usuario']['id']);
        if (count($errores) == 0) {
            if (!$modeloSesion->save(['id' => $_SESSION['usuario']['id'], 'username' => $_POST['user']])) {
                return redirect()->to('/account')->with('error', 'Error al cambiar el username');
            } else {
                $_SESSION['usuario']['username']= $_POST['user'];
                return redirect()->to('/account')->with('exito', 'Username cambiado correctamente');
            }
        } else {
            $data['errores'] = $errores;
            $data['titulo'] = 'Change Username';
            $data['label'] = 'Username';
            $data['name'] = 'user';
            unset($_POST['passVerify']);
            $data['input'] = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            var_dump($data['input']);
            return view('templates/left-menu.view.php') . view('settings.view.php', $data) . view('templates/footer.view.php');
        }
    }

    function changePassword() {
        $modeloSesion = new \App\Models\SessionModel();
        $errores = $this->checkPass($_POST, $_SESSION['usuario']['id']);
        if (count($errores) == 0) {
            $passCodificada = password_hash($_POST['pass'], PASSWORD_DEFAULT);
            if (!$modeloSesion->save(['id' => $_SESSION['usuario']['id'], 'pass' => $passCodificada])) {
                return redirect()->to('/account')->with('error', 'Error al cambiar la contraseña');
            } else {
                return redirect()->to('/account')->with('exito', 'Contraseña cambiada con exito');
            }
        } else {
            $data['errores'] = $errores;
            $data['titulo'] = 'Change Pass';
            $data['label'] = 'Password';
            $data['name'] = 'pass';
            return view('templates/left-menu.view.php') . view('settings.view.php', $data) . view('templates/footer.view.php');
        }
    }

    function deleteAccount(int $id_usuario) {
        $modelo = new \App\Models\SessionModel();
        if ($modelo->delete($id_usuario)) {
            return redirect()->to('/login');
        }
        return redirect()->to('/account')->with('error', 'Error al eliminar la cuenta');
    }

    function checkUsername(array $datos): array {
        $errores = [];
        $modelo = new \App\Models\SessionModel();
        if (!empty($datos['passVerify'])) {
            if (!$modelo->existePass($_SESSION['usuario']['id'], $datos['passVerify'])) {
                $errores['passVerify'] = 'La contraseña introducida no es correcta';
            }
        } else {
            $errores['passVerify'] = 'Introduce su contraseña';
        }
        if (!empty($datos['user'])) {
            if ($modelo->existeUsername($datos['user'])) {
                $errores['user'] = 'El username ya esta en uso';
            }
            if (!preg_match('/[0-9a-zA-Z_]{4,}/', $datos['user'])) {
                $errores['username'] = 'el username tiene que tener 4 caracteres minimo y solo puede estar formado por letras, numeros y  _';
            }
        } else {
            $errores['user'] = 'introduce un nombre de usuario';
        }
        if (!empty($datos['user2'])) {
            if (!preg_match('/[A-Za-z0-9_]{4,}/', $datos['user2'])) {
                $errores['user2'] = 'el username tiene que tener 4 caracteres minimo y solo puede estar formado por letras, numeros y  _';
            }
        } else {
            $errores['user2'] = 'introduce un nombre de usuario';
        }
        if ($datos['user'] != $datos['user2']) {
            $errores['user'] = 'Ambos usernames tienen que coincidir';
        }
        return $errores;
    }

    function checkPass(array $datos): array {
        $errores = [];
        $modelo = new \App\Models\SessionModel();
        if (!empty($datos['passVerify'])) {
            if (!$modelo->existePass($_SESSION['usuario']['id'], $datos['passVerify'])) {
                $errores['passVerify'] = 'La contraseña introducida no es correcta';
            }
        } else {
            $errores['passVerify'] = 'Introduce su contraseña';
        }
        if (!empty($datos['pass'])) {
            if (!preg_match('/[0-9a-zA-Z_]{8,}/', $datos['pass'])) {
                $errores['pass'] = 'la contraseña tiene que tener 8 caracteres minimo y solo puede estar formado por letras, numeros y  _';
            }
        } else {
            $errores['pass'] = 'introduce una contraseña';
        }
        if (!empty($datos['pass2'])) {
            if (!preg_match('/[0-9a-zA-Z_]{8,}/', $datos['pass2'])) {
                $errores['pass2'] = 'la contraseña tiene que tener 8 caracteres minimo y solo puede estar formado por letras, numeros y  _';
            }
        } else {
            $errores['pass2'] = 'introduce una contraseña';
        }
        if ($datos['pass'] != $datos['pass2']) {
            $errores['pass'] = 'Ambas contraseñas tienen que ser iguales';
        }
        if ($modelo->existePass($_SESSION['usuario']['id'], $datos['pass'])) {
            $errores['pass'] = 'La contraseña tiene que ser distinta a la anterior';
        }
        return $errores;
    }

    function checkForm(array $datos, int $id = 0): array {
        $errores = [];
        $actual = date("Y-m-d");
        $modelo = new \App\Models\HistorialPesoModel();

        if (empty($datos['fecha'])) {
            $errores['fecha'] = 'para editar tiene que introducir una fecha';
        } else {
            $fecha = explode('-', $datos['fecha']);
            if (!checkdate($fecha[1], $fecha[2], $fecha[0])) {
                $errores['fecha'] = 'Tiene que introducir una fecha correcta';
            }
            if ($datos['fecha'] > $actual) {
                $errores['fecha'] = 'Tiene que introducir una fecha menor o igual a la actual';
            }
            if ($modelo->existePesoDia($_SESSION['usuario']['id'], $datos['fecha'])) {
                $errores['fecha'] = 'no puede añadir esa fecha, porque ya esta ocupada por otro peso';
            }
            if (empty($datos['peso'])) {
                $errores['peso'] = 'tiene que introducir un peso en kg';
            } else {
                if (!is_numeric($datos['peso']) || $datos['peso'] < 10) {
                    $errores['peso'] = 'el peso tiene que ser un numero mayor que 10';
                }
            }
        }

        return $errores;
    }

}
