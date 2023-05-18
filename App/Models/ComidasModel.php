<?php

namespace App\Models;

class ComidasModel extends \App\Core\BaseModel {
    
    function getMealPlanDiaria(int $id_usuario,string $fecha):?array{
        $statement = $this->pdo->prepare('SELECT * FROM comidas WHERE id_usuario=? AND fecha_comida=?');
        $statement->execute([$id_usuario, $fecha]);
        return $statement->rowCount()>0 ? $statement->fetchAll():null;
    }
    
    function addComida(int $id_usuario,array $datos, string $mealType, string $fecha):bool{
        $statement = $this->pdo->prepare('INSERT INTO comidas (id_usuario, label, image, url, calorias, fecha_comida, totaltime, cuisinetype, ingredients, nombre_comida, nutrientes) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
        $statement->execute([$id_usuario,$datos['label'],$datos['image'],$datos['url'],$datos['calories'],$fecha,$datos['totalTime'],implode($datos['cuisineType']),json_encode($datos['ingredientes']),$mealType,json_encode($datos['totalNutrients'])]);
        return $statement->rowCount()==1;
    }
}

