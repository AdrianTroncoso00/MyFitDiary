<?php

namespace App\Models;

class RecetasFavoritasModel extends \App\Core\BaseModel {
    
    function getRecetasFavoritasUser(int $id_usuario):array{
        $statement = $this->pdo->prepare('SELECT * FROM recetas_favoritas WHERE id_usuario=?');
        $statement->execute([$id_usuario]);
        return ($statement->rowCount()!=0) ? $statement->fetchAll() : null;
    }
    
    function addRecetaFavorita(int $id_usuario ,array $datos):bool{
        var_dump($datos);
        $statement= $this->pdo->prepare('INSERT INTO recetas_favoritas (id_usuario, image, label, url, calories, totalTime, dietlabels, cuisinetype, ingredientlines) VALUES (?,?,?,?,?,?,?,?,?)');
        $statement->execute([$id_usuario, $datos['image'], $datos['label'], $datos['url'], $datos['calories'], $datos['totalTime'], $datos['dietLabels'], $datos['cuisineType'],$datos['ingredientLines']]);
        return $statement->rowCount()==1;
    }
}

