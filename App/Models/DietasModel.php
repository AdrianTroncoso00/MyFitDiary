<?php

namespace App\Models;

class DietasModel extends \App\Core\BaseModel {
    
    function getAllDietas():?array{
        $statement= $this->pdo->query('SELECT nombre_dieta FROM dietas');
        return $statement->fetchAll();
    }
}

