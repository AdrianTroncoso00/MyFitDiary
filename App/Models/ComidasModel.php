<?php

namespace App\Models;

class ComidasModel extends \App\Core\BaseModel {
    
    function getReceta(int $id_receta):?array{
        $statement= $this->pdo->prepare('SELECT * FROM comidas WHERE id_comida =?');
        $statement->execute([$id_receta]);
        return $statement->rowCount()>0 ? $statement->fetchAll()[0] : null;
    }
    
    function getComida(int $id_usuario, string $mealType, string $fecha):array{
        $statement = $this->pdo->prepare('SELECT * FROM comidas WHERE id_usuario=? AND nombre_comida LIKE ? AND fecha_comida = ?');
        $nombre_comida = $mealType.'%';
        $statement->execute([$id_usuario, $nombre_comida, $fecha]);
        return $statement->fetchAll();
    }
    
    function getMealPlanDiaria(int $id_usuario,string $fecha):?array{
        $statement = $this->pdo->prepare('SELECT * FROM comidas WHERE id_usuario=? AND fecha_comida=?');
        $statement->execute([$id_usuario, $fecha]);
        return $statement->rowCount()>0 ? $statement->fetchAll():null;
    }
    
    function addComida(int $id_usuario,array $datos, string $mealType, string $fecha):bool{
        $statement = $this->pdo->prepare('INSERT INTO comidas (id_usuario, label, image, url, calorias, fecha_comida, totaltime, cuisinetype,mealType ,ingredients, nombre_comida, nutrientes, yield, healthlabels) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $statement->execute([$id_usuario,$datos['label'],$datos['image'],$datos['url'],$datos['calorias'],$fecha,$datos['totalTime'],implode($datos['cuisineType']), implode(',', $datos['mealType']),json_encode($datos['ingredientes']),$mealType,json_encode($datos['nutrientes']),$datos['yield'], json_encode($datos['healthLabels'])]);
        return $statement->rowCount()==1;
    }
    
    function modificarRecetaEspecifica(int $id_comida, array $datos):bool{
        $statement = $this->pdo->prepare('UPDATE comidas SET label=?, image=?, url=?, calorias=?, totaltime=?, yield=?,cuisinetype=?, ingredients=?, nutrientes=? WHERE id_comida=?');
        $statement->execute([$datos['label'],$datos['image'],$datos['url'],$datos['calorias'],$datos['totalTime'], $datos['yield'],implode($datos['cuisineType']),json_encode($datos['ingredientes']),json_encode($datos['nutrientes']), $id_comida]);
        return $statement->rowCount()==1;
    }
    
    function modificarComida(int $id_usuario, string $fecha ,string $mealType, array $datos):bool{
        $statement = $this->pdo->prepare('UPDATE comidas SET label=?, image=?, url=?, calorias=?, totaltime=?, yield=?,cuisinetype=?, ingredients=?, nutrientes=? WHERE id_usuario=? AND fecha_comida=? AND nombre_comida = ?');
        $statement->execute([$datos['label'],$datos['image'],$datos['url'],$datos['calorias'],$datos['totalTime'],$datos['yield'],implode(',',$datos['cuisineType']),json_encode($datos['ingredientes']),json_encode($datos['nutrientes']),$id_usuario,$fecha,$mealType]);
        return $statement->rowCount()==1;
    }
    
    function deleteComidaEspecifica(int $id_comida):bool{
        $statement = $this->pdo->prepare('DELETE FROM comidas WHERE id_comida=?');
        $statement->execute([$id_comida]);
        return $statement->rowCount()==1;
    }
    
    function existeComidaDia(string $fecha, int $id_usuario, string $mealType):bool{
        $statemetn  = $this->pdo->prepare('SELECT * FROM comidas WHERE id_usuario=? AND fecha_comida =? AND nombre_comida LIKE ?');
        $param = $mealType.'%';
        $statemetn->execute([$id_usuario, $fecha, $param]);
        return $statemetn->rowCount()>0;
    }
    
    function eliminarComidasPasadas(string $dia):bool{
        $statement = $this->pdo->prepare('DELETE FROM comidas WHERE fecha_comida<?');
        $statement->execute([$dia]);
        return $statement->rowCount()>0;
    }
    
    function existeComidaSemana(int $id_usuario, string $nombreComida, string $label, string $fecha):bool{
        $fechaFinal = date("Y-m-d", strtotime($fecha."-1 week"));
        $statement = $this->pdo->prepare('SELECT * FROM comidas WHERE id_usuario=? AND nombre_comida LIKE ? AND label =? AND fecha_comida >= ? AND fecha_comida <=?');
        $statement->execute([$id_usuario, $nombreComida, $label,$fechaFinal, $fecha]);
        return $statement->rowCount()>0;
    }
    function getComidasSemana(int $id_usuario,string $nombreComida,string $diaInicioSemana, string $diaFinalSemana):?array{
        $nombreComidaProcesada = $nombreComida=='dinner' ? '%'.$nombreComida : $nombreComida.'%';
        $statement = $this->pdo->prepare('SELECT * FROM comidas WHERE id_usuario=? AND mealType LIKE ? AND fecha_comida >= ? AND fecha_comida <=?');
        $statement->execute([$id_usuario,$nombreComidaProcesada,$diaInicioSemana, $diaFinalSemana]);
        return $statement->rowCount()>0 ? $statement->fetchAll() : null;
    }
    
    function existeRecetaEspecificaDia(int $id_usuario, string $mealType, string $label, string $fecha):bool{
        $statement = $this->pdo->prepare('SELECT * FROM comidas WHERE id_usuario=? AND nombre_comida LIKE ? AND label=? AND fecha_comida=?');
        $nombreComida = $mealType.'%';
        $statement->execute([$id_usuario,$nombreComida,$label,$fecha]);
        return $statement->rowCount()==1;
    }
    
    function deleteComidasSemanaAnterior(int $id_usuario, string $fecha):bool{
        $fechaEliminar = date("Y-m-d", strtotime($fecha."-1 week"));
        $statement = $this->pdo->prepare('DELETE FROM comidas WHERE id_usuario=? AND fecha_comida<?');
        $statement->execute([$id_usuario,$fechaEliminar]);
        return $statement->rowCount()>0;
    }
    
    function getMealPlanSemana(int $id_usuario, array $semana):?array{
        $statement = $this->pdo->prepare('SELECT * FROM comidas WHERE id_usuario=? AND fecha_comida>=? AND fecha_comida<=?');
        $statement->execute([$id_usuario,$semana[0], $semana[6]]);
        return $statement->rowCount()>0 ? $statement->fetchAll() : null;
        
    }
}

