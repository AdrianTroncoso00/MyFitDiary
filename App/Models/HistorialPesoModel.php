<?php

namespace App\Models;

class HistorialPesoModel extends \CodeIgniter\Model{
    
    protected $table = 'historial_peso';
    protected $primaryKey = 'id_peso';
    protected $allowedFields = ['id_usuario','peso','fecha'];
    
    function getPesosUsuario(int $id): ?array{
        return $this->asArray()->where('id_usuario',$id)->orderBy('fecha')->findAll();
    }

    function existePesoDia(int $id, string $fecha):bool{
        $user = $this->select('peso')->where('id_usuario',$id)->where('fecha',$fecha)->find();
        return count($user)>0; 
    }

}

