<?php

namespace App\Models;

class RelAlergenosModel extends \CodeIgniter\Model {
    
    protected $table = 'rel_alergenos';
    protected $primaryKey = 'id_rel_alergeno';
    protected $allowedFields = ['id_usuario', 'alergeno'];
    
    function getAlergenos():?array{
        return $this->asArray()->findAll();
    }
    function getAlergenosUsuario(int $id):?array{
        return $this->asArray()->where(['id_usuario'=>$id])->findColumn('alergeno');
    }
    function getIdAlergenosUsuario(int $id): ?array{
        return $this->asArray()->where(['id_usuario'=>$id])->findColumn('id_rel_alergeno');
        
    }
    function addAlergenoUser(int $alergenos, int $id):bool{
        $statement = $this->pdo->prepare('INSERT INTO rel_alergenos (id_usuario, alergeno) VALUES (?,?)');
        $statement->execute([$id, $alergenos]);
        return $statement->rowCount()==1;
    }
    
}

