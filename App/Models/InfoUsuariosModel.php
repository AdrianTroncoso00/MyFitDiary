<?php

namespace App\Models;

class InfoUsuariosModel extends \CodeIgniter\Model {
    
    protected $table = 'info_usuarios';
    protected $primaryKey = 'id_usuario';
    protected $useAutoIncrement = false;
    protected $allowedFields = ['id_usuario','genero', 'objetivo','edad','peso','estatura','actividad_fisica','metabolismo_basal','calorias_mantenimiento','calorias_objetivo','nombre_completo','dieta','num_comidas','porcent_breakfast','porcent_brunch','porcent_lunch','porcent_snack','porcent_dinner'];
    
    function getAllInfoUsuario(int $id_usuario):array{
        return $this->asArray()->where(['id_usuario'=>$id_usuario])->find();
    }
//    function addInfoUsuario(array $datos, int $id_usuario, float $imc, array $calorias):bool{
//        $statement = $this->pdo->prepare('INSERT INTO info_usuarios (genero, objetivo, edad, id_usuario, imc, dieta, metabolismo_basal, calorias_mantenimiento, calorias_objetivo, peso, estatura, actividad_fisica, nombre_completo, num_comidas, porcent_breakfast, porcent_brunch, porcent_lunch, porcent_snack, porcent_dinner) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
//        $statement->execute([$datos['genero'], $datos['meta'], $datos['edad'], $id_usuario, $imc, $datos['dietas'],$calorias['tmb'], $calorias['caloriasMantenimiento'], $calorias['caloriasObjetivo'], $datos['peso'], $datos['altura'], $datos['actividad'], $datos['nombre'], $datos['num_comidas'], $datos['porcent_breakfast'], $datos['porcent_brunch'], $datos['porcent_lunch'], $datos['porcent_snack'], $datos['porcent_dinner']]);
//        return $statement->rowCount()==1;
//    }
//
//    
//    function setPeso(int $id_usuario, int $peso):bool{
//        $statement = $this->pdo->prepare('UPDATE info_usuarios SET peso=? WHERE id_usuario=?');
//        $statement->execute([$peso, $id_usuario]);
//        return $statement->rowCount()==1;
//    }
}

