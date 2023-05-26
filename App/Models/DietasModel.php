<?php

namespace App\Models;

class DietasModel extends \App\Core\BaseModel {
    
    function getAllDietas():?array{
        $statement= $this->pdo->query('SELECT * FROM dietas');
        return $statement->fetchAll();
    }
    
    function getAllIdDietas():array{
        $statement= $this->pdo->query('SELECT id_dieta FROM dietas');
        return $statement->fetchAll();
    }
}

