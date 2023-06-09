<?php

namespace App\Models;

use \PDOException;

class SessionModel extends \CodeIgniter\Model {
    
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    protected $allowedFields = ['email', 'username', 'pass', 'last_login','rol'];
    
    function getAllUsuario(int $id_usuario):?array{
        $user= $this->asArray()->select('*')->join('info_usuarios', 'usuarios.id = info_usuarios.id_usuario', 'left')->join('act_fisica', 'info_usuarios.actividad_fisica = act_fisica.id_actividad', 'left')->join('dietas', 'info_usuarios.dieta = dietas.id_dieta')->where(['id_usuario'=>$id_usuario])->findAll()[0];
        var_dump($user);
        unset($user['pass']);
        return $user;
    }
    
    function getUser(string $email):?array{
        $user= $this->asArray()->where(['email'=>$email])->findAll()[0];
        unset($user['pass']);
        return $user;
    }

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

    function existePass(int $id_usuario,string $pass):bool{
        $user = $this->asArray()->select('pass')->where('id',$id_usuario)->find()[0];
        return password_verify($pass, $user['pass']);
    }
    function existeParam(string $parametro,string $username):bool{
        $user = $this->select($parametro)->where($parametro,$username)->find();
        return count($user)>0;
    }
}
