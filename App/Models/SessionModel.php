<?php

namespace App\Models;

use \PDOException;

class SessionModel extends \CodeIgniter\Model {
    
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    protected $allowedFields = ['email', 'username', 'pass', 'last_login'];
    
    function getAllUsuario(int $id_usuario):?array{
        $user= $this->asArray()->select('*')->join('info_usuarios', 'usuarios.id = info_usuarios.id_usuario', 'left')->join('act_fisica', 'info_usuarios.actividad_fisica = act_fisica.id_actividad', 'left')->join('dietas', 'info_usuarios.dieta = dietas.id_dieta')->where(['id_usuario'=>$id_usuario])->findAll()[0];
        unset($user['pass']);
        return $user;
    }
    
    function existeParametro(string $param, string $elemento): bool {
        $statement = $this->pdo->prepare("SELECT * FROM usuarios WHERE $param=?");
        $statement->execute([$elemento]);
        return ($statement->rowCount() > 0) ? true : false;
    }
    
    function getAlergenos(int $id_usuario):?array{
        $statement = $this->pdo->prepare("SELECT nombre_alergeno FROM alergenos LEFT JOIN rel_alergenos ON alergenos.id_alergenos = rel_alergenos.alergeno WHERE id_usuario=?");
        $statement->execute([$id_usuario]);
        return ($statement->rowCount() > 0) ? $statement->fetchAll() : null;  
    }

//    function login(string $email, string $pass): ?array {
//        $statement = $this->pdo->prepare(self::SELECT_USUARIOS_INFO_USUARIOS . self::LEFT_JOIN ." WHERE email=?");
//        $statement->execute([$email]);
//        if ($statement->rowCount() > 0) {
//            $usuario = $statement->fetchAll()[0];
//            if (password_verify($pass, $usuario['pass'])) {
//                unset($usuario['pass']);
//                return $usuario;
//            }
//        }
//        return null;
//    }
    function login(string $email, string $pass): ?array {
        $user= $this->asArray()->select('*')->join('info_usuarios', 'usuarios.id = info_usuarios.id_usuario', 'left')->join('act_fisica', 'info_usuarios.actividad_fisica = act_fisica.id_actividad', 'left')->join('dietas', 'info_usuarios.dieta = dietas.id_dieta')->where(['email'=>$email])->findAll()[0];
        if(password_verify($pass, $user['pass'])){
            unset($user['pass']);
            return $user;
        }
        return null;
    }

    function signUp(array $datos): bool {
        $statement = $this->pdo->prepare("INSERT INTO usuarios(email, username, pass, rol) VALUES (?,?,?,?)");
        $passCodificada = password_hash($datos['pass'], PASSWORD_DEFAULT);
        $statement->execute([$datos['email'], $datos['username'], $passCodificada, 'standart']);
        if ($statement->rowCount() == 1) {
            return true;
        }
        return false;
    }
    
    function updateLastDate(int $id):bool{
        $statement = $this->pdo->prepare('UPDATE usuarios SET last_login = SYSDATE() WHERE id=?');
        $statement->execute([$id]);
        return ($statement->rowCount()>0) ? true : false;
    }
    
    function changePass(string $pass, int $id): bool{
        $statement = $this->pdo->prepare('UPDATE usuarios SET pass=? WHERE id=?');
        $passCodificada = password_hash($pass, PASSWORD_DEFAULT);
        $statement->execute([$passCodificada, $id]);
        return $statement->rowCount()==1;   
    }
    
    function changeUsername(string $username, int $id){
        $statement = $this->pdo->prepare('UPDATE usuarios SET username=? WHERE id=?');
        $statement->execute([$username, $id]);
        return $statement->rowCount()==1;
    }
    
    function getIdByEmail(string $email):int{
        $statement = $this->pdo->prepare('SELECT id FROM usuarios WHERE email=?');
        $statement->execute([$email]);
        return $statement->rowCount()>0 ? $statement->fetchAll()[0]['id'] : 0;
    }
    
    function getInfoById(int $id):?array{
        $statement = $this->pdo->prepare(self::SELECT_USUARIOS_INFO_USUARIOS . self::LEFT_JOIN ." WHERE id=?");
        $statement->execute([$id]);
        return $statement->rowCount()>0 ? $statement->fetchAll()[0] : null;
    }
    
    function existePass(int $id_usuario,string $pass):bool{
        $statement = $this->pdo->prepare('SELECT pass FROM usuarios WHERE id=?');
        $statement->execute([$id_usuario]);
        $password = $statement->fetch();
        return password_verify($pass, $password['pass']);
    }
    function existeUsername(string $username):bool{
        $statement = $this->pdo->prepare('SELECT username FROM usuarios WHERE username=?');
        $statement->execute([$username]);
        return $statement->rowCount()==1;
    }
    
    function deleteUser(int $id_usuario):bool{
        $statement = $this->pdo->prepare('DELETE FROM usuarios WHERE id=?');
        $statement->execute([$id_usuario]);
        return $statement->rowCount()==1;
    }
}
