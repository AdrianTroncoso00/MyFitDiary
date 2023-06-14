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
        $data['descript'] = 'Use the form below to change your password. Your new password cannot be the same as your actual password.';
        $data['label'] = 'Password';
        $data['name'] = 'pass';
        return view('templates/left-menu.view.php') . view('settings.view.php', $data) . view('templates/footer.view.php');
    }

    function showFormChangeUsername() {
        $data['titulo'] = 'Change Username';
        $data['descript'] = 'Use the form below to change your username. Your new username cannot be the same as your actual username.';
        $data['label'] = 'Username';
        $data['name'] = 'user';
        return view('templates/left-menu.view.php') . view('settings.view.php', $data) . view('templates/footer.view.php');
    }

    function showFormDeleteAccount() {
        $data['titulo'] = 'Delete Account';
        $data['descript'] = 'Are you sure you want to delete your account permanently? This will result in all your information being lost and it wont be possible to recover it later.';
        $data['deleteAccount']=true;
        return view('templates/left-menu.view.php') . view('settings.view.php', $data) . view('templates/footer.view.php');
    }

    function showAccount() {
        $data = $this->showData();
        return view('templates/left-menu.view.php') . view('account-details.view.php', $data) . view('templates/footer.view.php');
    }

    function addPeso() {
        $errores = $this->checkForm($_POST);
        $modeloInfoUsuarios = new \App\Models\InfoUsuariosModel();
        if (count($errores) == 0) {
            $modelo = new \App\Models\HistorialPesoModel();
            if ($modelo->save(['id_usuario' => $_SESSION['usuario']['id'], 'peso' => $_POST['peso'], 'fecha' => $_POST['fecha']])) {
                return $modeloInfoUsuarios->save(['id_usuario' => $_SESSION['usuario']['id'], 'peso' => $_POST['peso']]) ? redirect()->to('/account')->with('exito', 'Weight added successfully.') : redirect()->to('/account')->with('error', 'The weight couldnt be saved.');
            }
            return redirect()->to('/account')->with('error', 'The weight couldnt be saved.');
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
            return redirect()->to('/account')->with('error', 'The weight couldnt be deleted. Please try again.');
        }
        return redirect()->to('/account')->with('exito', 'Weight deleted successfully.');
    }

    function changeUsername() {
        $modeloSesion = new \App\Models\SessionModel();
        $errores = $this->checkUsername($_POST, $_SESSION['usuario']['id']);
        if (count($errores) == 0) {
            if (!$modeloSesion->save(['id' => $_SESSION['usuario']['id'], 'username' => $_POST['user']])) {
                return redirect()->to('/account')->with('error', 'Error occurred while changing the username.');
            } else {
                $_SESSION['usuario']['username'] = $_POST['user'];
                return redirect()->to('/account')->with('exito', 'Username changed successfully.');
            }
        } else {
            $data['errores'] = $errores;
            $data['titulo'] = 'Change Username';
            $data['descript'] = 'Use the form below to change your username. Your new username cannot be the same as your actual username.';
            $data['label'] = 'Username';
            $data['name'] = 'user';
            unset($_POST['passVerify']);
            $data['input'] = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            return view('templates/left-menu.view.php') . view('settings.view.php', $data) . view('templates/footer.view.php');
        }
    }

    function changePassword() {
        $modeloSesion = new \App\Models\SessionModel();
        $errores = $this->checkPass($_POST, true);
        if (count($errores) == 0) {
            $passCodificada = password_hash($_POST['pass'], PASSWORD_DEFAULT);
            if (!$modeloSesion->save(['id' => $_SESSION['usuario']['id'], 'pass' => $passCodificada])) {
                return redirect()->to('/account')->with('error', 'Error ocurred while changing the password');
            } else {
                return redirect()->to('/account')->with('exito', 'Password changed successfully.');
            }
        } else {
            $data['errores'] = $errores;
            $data['titulo'] = 'Change Pass';
            $data['descript'] = 'Use the form below to change your username. Your new username cannot be the same as your actual username.';
            $data['label'] = 'Password';
            $data['name'] = 'pass';
            return view('templates/left-menu.view.php') . view('settings.view.php', $data) . view('templates/footer.view.php');
        }
    }

    function deleteAccount() {
        $modelo = new \App\Models\SessionModel();

        $errores = $this->checkPass($_POST, true);
        if (count($errores) == 0) {
            if ($modelo->delete($_SESSION['usuario']['id'])) {
                session_destroy();
                return redirect()->to('/login');
            }
        }
        $data['errores'] = $errores;
        $data['titulo'] = 'Delete Account';
        $data['descript'] = 'Use the form below to change your username. Your new username cannot be the same as your actual username.';
        return view('templates/left-menu.view.php') . view('settings.view.php', $data) . view('templates/footer.view.php');
    }

    function checkUsername(array $datos): array {
        $errores = [];
        $modelo = new \App\Models\SessionModel();
        if (!empty($datos['passVerify'])) {
            if (!$modelo->existePass($_SESSION['usuario']['id'], $datos['passVerify'])) {
                $errores['passVerify'] = 'The password is incorrect';
            }
        } else {
            $errores['passVerify'] = 'Enter a password';
        }
        if (!empty($datos['user'])) {
            if ($modelo->existeParam('username', $datos['user'])) {
                $errores['user'] = 'The username is already in use.';
            }
            if (!preg_match('/[0-9a-zA-Z_]{4,}/', $datos['user'])) {
                $errores['username'] = 'The username must have a minimum of 4 characters and can only consist of letters, numbers, and _.';
            }
        } else {
            $errores['user'] = 'introduce  a userneme';
        }
        if (!empty($datos['user2'])) {
            if (!preg_match('/[A-Za-z0-9_]{4,}/', $datos['user2'])) {
                $errores['user2'] = 'The username must have a minimum of 4 characters and can only consist of letters, numbers, and _.';
            }
        } else {
            $errores['user2'] = 'introduce a userneme';
        }
        if ($datos['user'] != $datos['user2']) {
            $errores['user'] = 'Both usernames must match.';
        }
        return $errores;
    }

    function checkPass(array $datos, bool $delete): array {
        $errores = [];
        $modelo = new \App\Models\SessionModel();
        if (!$delete) {
            if (!empty($datos['passVerify'])) {
                if (!$modelo->existePass($_SESSION['usuario']['id'], $datos['passVerify'])) {
                    $errores['passVerify'] = 'The password is incorrect';
                }
            } else {
                $errores['passVerify'] = 'Enter a password';
            }
            if (!empty($datos['pass'])) {
                if (!preg_match('/[0-9a-zA-Z_]{8,}/', $datos['pass'])) {
                    $errores['pass'] = 'The password must be a minimum of 8 characters and can only consist of letters, numbers, and _.';
                }
            } else {
                $errores['pass'] = 'Enter a password';
            }
            if (!empty($datos['pass2'])) {
                if (!preg_match('/[0-9a-zA-Z_]{8,}/', $datos['pass2'])) {
                    $errores['pass2'] = 'The password must be a minimum of 8 characters and can only consist of letters, numbers, and _.';
                }
            } else {
                $errores['pass2'] = 'Enter a password';
            }
            if ($datos['pass'] != $datos['pass2']) {
                $errores['pass'] = 'Both passwords must be the same.';
            }
            if ($modelo->existePass($_SESSION['usuario']['id'], $datos['pass'])) {
                $errores['pass'] = 'The password must be different from the previous one.';
            }
        } else {
            if (!empty($datos['passVerify'])) {
                if (!$modelo->existePass($_SESSION['usuario']['id'], $datos['passVerify'])) {
                    $errores['passVerify'] = 'The password is incorrect.';
                }
            } else {
                $errores['passVerify'] = 'Enter a password.';
            }
        }
        return $errores;
    }

    function checkForm(array $datos, int $id = 0): array {
        $errores = [];
        $actual = date("Y-m-d");
        $modelo = new \App\Models\HistorialPesoModel();

        if (empty($datos['fecha'])) {
            $errores['fecha'] = 'You need to enter a date.';
        } else {
            $fecha = explode('-', $datos['fecha']);
            if (!checkdate($fecha[1], $fecha[2], $fecha[0])) {
                $errores['fecha'] = 'You need to enter a valid date.';
            }
            if ($datos['fecha'] > $actual) {
                $errores['fecha'] = 'You need to enter a date that is less than or equal to the current date.';
            }
            if ($modelo->existePesoDia($_SESSION['usuario']['id'], $datos['fecha'])) {
                $errores['fecha'] = 'You cannot add that date because it is already occupied by another weight entry.';
            }
            if (empty($datos['peso'])) {
                $errores['peso'] = 'You need to enter a weight in kilograms.';
            } else {
                if (!is_numeric($datos['peso']) || $datos['peso'] < 10) {
                    $errores['peso'] = 'The weight must be a number greater than 10.';
                }
            }
        }

        return $errores;
    }

}
