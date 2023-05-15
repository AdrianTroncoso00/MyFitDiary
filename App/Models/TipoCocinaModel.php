<?php

namespace App\Models;

class TipoCocinaModel extends \App\Core\BaseModel {
    
    function getAll():array{
        $statement = $this->pdo->query('SELECT nombre_tipo_cocina FROM tipo_cocina');
        return $statement->fetchAll();
    }
}

