<?php

namespace App\Models; 

class NombreComidasModel extends \App\Core\BaseModel{
    
    function getAllNombresComidas():array{
        $statement= $this->pdo->query('SELECT nombre_comida FROM nombre_comidas');
        return $statement->fetchAll();
    }
}

