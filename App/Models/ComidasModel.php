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
        $statement->execute([$id_usuario,$datos['label'],$datos['image'],$datos['url'],$datos['calorias'],$fecha,$datos['totalTime'],implode($datos['cuisineType']),json_encode($datos['ingredientes']),$mealType,json_encode($datos['nutrientes'])]);
        return $statement->rowCount()==1;
    }
    
    function modificarComida(int $id_comida, array $datos, string $mealType):bool{
        $statement = $this->pdo->prepare('UPDATE comidas SET label=?, image=?, url=?, calorias=?, totaltime=?, cuisinetype=?, ingredients=?, nutrientes=? WHERE id_comida=?');
        $statement->execute([$datos['label'],$datos['image'],$datos['url'],$datos['calorias'],$datos['totalTime'],implode($datos['cuisineType']),json_encode($datos['ingredientes']),$mealType,json_encode($datos['nutrientes']), $id_comida]);
        return $statement->rowCount()==1;
    }
    
    function deleteComida(int $id_usuario, string $fecha ,string $mealType):bool{
        $statement = $this->pdo->prepare('DELETE FROM comidas WHERE id_usuario=? AND fecha_comida=? AND nombre_comida LIKE ?');
        $param = $mealType.'%';
        $statement->execute([$id_usuario,$fecha,$param]);
        return $statement->rowCount()==1;
    }
    
    function deleteComidaEspecifica(int $id_comida):bool{
        $statement = $this->pdo->prepare('DELETE FROM comidas WHERE id_comida=?');
        $statement->execute([$id_comida]);
        return $statement->rowCount()==1;
    }
}

