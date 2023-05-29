<?php

namespace App\Models;

class AlergenosModel extends \App\Core\BaseModel {
    
    function getAll():array{
        $statement = $this->pdo->query('SELECT * FROM alergenos');
        return $statement->fetchAll();
    }
    
    function getAllIdAlergenos():array{
        $statement = $this->pdo->query('SELECT id_alergenos FROM alergenos');
        return $statement->fetchAll();
    }
    
    
}

