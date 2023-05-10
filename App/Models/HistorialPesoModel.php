<?php

namespace App\Models;

class HistorialPesoModel extends \App\Core\BaseModel{
    
    function addNewPeso(int $id, float $kg, string $fecha):bool{
        $statement = $this->pdo->prepare('INSERT INTO historial_peso (id_usuario,peso, fecha) VALUES (?,?,?)');
        $statement->execute([$id, $kg, $fecha]);
        return ($statement->rowCount()>0) ? true : false;
    }
    
    function getPesosUsuario(int $id): ?array{
        $statement = $this->pdo->prepare('SELECT * FROM historial_peso WHERE id_usuario=? ORDER BY fecha');
        $statement->execute([$id]);
        return ($statement->rowCount()>0) ?  $statement->fetchAll() :null;
    }
    
    function deletePeso(int $id_peso):bool{
        $statement = $this->pdo->prepare('DELETE FROM historial_peso WHERE id_peso=?');
        $statement->execute([$id_peso]);
        return $statement->rowCount()==1;
    }
    
    function existePesoDia(int $id, string $fecha):bool{
        $statement = $this->pdo->prepare('SELECT * FROM historial_peso WHERE id_usuario=? AND fecha=?');
        $statement->execute([$id, $fecha]);
        return $statement->rowCount()==1;
        
    }
    function existePesoDiaEdit(int $id_usuario,int $id, string $fecha):bool{
        $statement = $this->pdo->prepare('SELECT * FROM historial_peso WHERE fecha=? AND id_peso!=?');
        $statement->execute([$id_usuario, $fecha, $id]);
        return $statement->rowCount()==1;
        
    }
    
    function getPeso(int $id):?array{
        $statement = $this->pdo->prepare('SELECT * FROM historial_peso WHERE id_peso=?');
        $statement->execute([$id]);
        return ($statement->rowCount()>0) ? $statement->fetchAll()[0] : null;
    }
}

