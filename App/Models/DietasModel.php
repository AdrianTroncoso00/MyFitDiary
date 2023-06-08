<?php

namespace App\Models;

class DietasModel extends \CodeIgniter\Model {

    protected $table = 'dietas';
    protected $primaryKey = 'id_dieta';
    protected $allowedFields = ['id_dieta', 'nombre_dieta'];

    function getAllDietas(): ?array {
        return $this->asArray()->findAll();
    }

    function getAllIdDietas():array{
        return $this->asArray()->findColumn('id_dieta');
    }

}
