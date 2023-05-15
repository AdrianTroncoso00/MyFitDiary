<?php

namespace App\Models;

class TipoComidaModel extends \App\Core\BaseModel {
   
    function getAll():array{
        $statement = $this->pdo->query('SELECT nombre_tipo_comida FROM tipo_comida');
        return $statement->fetchAll();
    }
}

