<?php

namespace App\Models;

class AlergenosModel extends \App\Core\BaseModel {
    
    function getAll():array{
        $statement = $this->pdo->query('SELECT nombre_alergeno FROM alergenos');
        return $statement->fetchAll();
    }
}

