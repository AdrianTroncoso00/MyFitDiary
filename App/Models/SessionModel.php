<?php

namespace App\Models;

use \PDOException;

class SessionModel extends \App\Core\BaseModel {
    
    const SELECT_USUARIOS_INFO_USUARIOS = 'SELECT usuarios.*, info_usuarios.genero, info_usuarios.objetivo, info_usuarios.edad, info_usuarios.imc, info_usuarios.metabolismo_basal, info_usuarios.calorias_mantenimiento,'
            . 'info_usuarios.calorias_objetivo, info_usuarios.peso, info_usuarios.estatura, info_usuarios.nombre_completo, info_usuarios.num_comidas, act_fisica.descripcion_actividad, dietas.nombre_dieta, info_usuarios.porcent_breakfast, info_usuarios.porcent_snack, info_usuarios.porcent_lunch, info_usuarios.porcent_brunch, info_usuarios.porcent_dinner';
    
    const LEFT_JOIN =' FROM usuarios LEFT JOIN info_usuarios ON usuarios.id = info_usuarios.id_usuario LEFT JOIN act_fisica ON info_usuarios.actividad_fisica = act_fisica.id_actividad LEFT JOIN dietas ON info_usuarios.dieta = dietas.id_dieta' ;
    
    function existeParametro(string $param, string $elemento): bool {
        $statement = $this->pdo->prepare("SELECT * FROM usuarios WHERE $param=?");
        $statement->execute([$elemento]);
        return ($statement->rowCount() > 0) ? true : false;
    }

    function login(string $email, string $pass): ?array {
        $statement = $this->pdo->prepare(self::SELECT_USUARIOS_INFO_USUARIOS . self::LEFT_JOIN ." WHERE email=?");
        $statement->execute([$email]);
        if ($statement->rowCount() > 0) {
            $usuario = $statement->fetchAll()[0];
            if (password_verify($pass, $usuario['pass'])) {
                unset($usuario['pass']);
                return $usuario;
            }
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
    
    function getIdByEmail(string $email):int{
        $statement = $this->pdo->prepare('SELECT id FROM usuarios WHERE email=?');
        $statement->execute([$email]);
        if($statement->rowCount()>0){
            return $statement->fetchAll()[0]['id'];
        }
        return 0;
    }

}
