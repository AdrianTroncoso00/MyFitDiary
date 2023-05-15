<?php

namespace App\Models;

class DietaDiaModel extends \App\Core\BaseModel {
    
    function getAllDietas(){
        $statement = $this->pdo->query('SELECT nombre_dieta FROM dietas');
        return $statement->fetchAll();
    }
    
    function addDietaDia(int $id, array $datos):bool{
        $brunch = isset($datos['brunch']) ? $datos['brunch'] : null;
        $merienda = isset($datos['merienda']) ? $datos['merienda'] : null;
        $statement = $this->pdo->prepare('INSERT INTO dieta_dia (id_usuario, desayuno, brunch, comida, merienda, cena, dia_receta) VALUES(?,?,?,?,?,?,SYSDATE())');
        $statement->execute([$id, json_encode($datos['desayuno']), $brunch, json_encode($datos['comida']), $merienda, json_encode($datos['cena'])]);
        return $statement->rowCount()==1;
    }
}

