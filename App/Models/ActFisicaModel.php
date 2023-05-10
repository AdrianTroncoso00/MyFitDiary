<?php

namespace App\Models;

class ActFisicaModel extends \App\Core\BaseModel{
    
    function getAllActFisica():array{
        $statement = $this->pdo->query('SELECT * FROM act_fisica');
        return $statement->fetchAll();
    }
    function getAllId():array{
        $statement = $this->pdo->query('SELECT id_actividad FROM act_fisica');
        return $statement->fetchAll();
    }
}
