<?php

namespace App\Models;

class ActFisicaModel extends \CodeIgniter\Model{
    
    protected $table = 'act_fisica';
    protected $primaryKey = 'id_actividad';
    protected $allowedFields = ['id_actividad', 'descripcion_actividad'];

    function getAllActFisica(): ?array {
        return $this->asArray()->findAll();
    }

    function getAllIdActFisica():array{
        return $this->asArray()->findColumn('id_actividad');
    }
}
