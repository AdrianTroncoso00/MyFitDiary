<?php

namespace App\Models;

class AlergenosModel extends \CodeIgniter\Model {
    
    protected $table = 'alergenos';
    protected $primaryKey = 'id_alergenos';
    protected $allowedFields = ['id_alergenos', 'nombre_alergeno'];
    function getAll():?array{
        return $this->asArray()->findAll();
    }
    
    function getAlergenosUser(int $id_usuario): ?array{
        return $this->asArray()->select('nombre_alergeno')->join('rel_alergenos', 'alergenos.id_alergenos = rel_alergenos.alergeno','left')->where(['id_usuario'=>$id_usuario])->findAll();
    }
    
    function getAllIdAlergenos():?array{
       return $this->asArray()->findColumn('id_alergenos');
    }
    
}

