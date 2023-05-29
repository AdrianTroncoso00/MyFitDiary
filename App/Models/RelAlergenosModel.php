<?php

namespace App\Models;

class RelAlergenosModel extends \App\Core\BaseModel {
    
    function getAlergenosUsuario(int $id):?array{
        $statement = $this->pdo->prepare('SELECT alergeno FROM rel_alergenos WHERE id_usuario =?');
        $statement->execute([$id]);
        return $statement->rowCount()>0 ? $statement->fetchAll() : null;
    }
    
    function addAlergenoUser(int $alergenos, int $id):bool{
        $statement = $this->pdo->prepare('INSERT INTO rel_alergenos (id_usuario, alergeno) VALUES (?,?)');
        $statement->execute([$id, $alergenos]);
        return $statement->rowCount()==1;
    }
    
}

