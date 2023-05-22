<?php

namespace App\Models;

class AlergenosModel extends \App\Core\BaseModel {
    
    function getAll():array{
        $statement = $this->pdo->query('SELECT * FROM alergenos');
        return $statement->fetchAll();
    }
}

