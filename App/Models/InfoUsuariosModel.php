<?php

namespace App\Models;

class InfoUsuariosModel extends \App\Core\BaseModel {
    
    function addInfoUsuario(array $datos, int $id_usuario, float $imc, array $calorias):bool{
        $statement = $this->pdo->prepare('INSERT INTO info_usuarios (genero, objetivo, edad, id_usuario, imc, dieta, metabolismo_basal, calorias_mantenimiento, calorias_objetivo, peso, estatura, actividad_fisica, nombre_completo, num_comidas, porcent_breakfast, porcent_brunch, porcent_lunch, porcent_snack, porcent_dinner) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $statement->execute([$datos['genero'], $datos['meta'], $datos['edad'], $id_usuario, $imc, $datos['dietas'],$calorias['tmb'], $calorias['caloriasMantenimiento'], $calorias['caloriasObjetivo'], $datos['peso'], $datos['altura'], $datos['actividad'], $datos['nombre'], $datos['num_comidas'], $datos['porcent_breakfast'], $datos['porcent_brunch'], $datos['porcent_lunch'], $datos['porcent_snack'], $datos['porcent_dinner']]);
        return $statement->rowCount()==1;
    }
    
    function getQuery(array $datos):array{
        $query=[];
        if(!empty($datos['actividad'])){
            $query['actividad']= 'actividad';
        }
        if(!empty($datos['num_comidas'])){
            $query['num_comidas']= 'num_comidas';
        }
        if(!empty($datos['dietas'])){
            $query['dieta']= 'dieta';
        }
        if(!empty($datos['porcent_brakfast'])){
            $query['porcent_breakfast']= 'porcent_breakfast';
        }
        if(!empty($datos['porcent_brunch'])){
            $query['porcent_brunch']= 'porcent_brunch';
        }
        if(!empty($datos['porcent_lunch'])){
            $query['porcent_lunch']= 'porcent_lunch';
        }
        if(!empty($datos['porcent_snack'])){
            $query['porcent_snack']= 'porcent_snack';
        }
        if(!empty($datos['porcent_dinner'])){
            $query['porcent_dinner']= 'porcent_dinner';
        }
        return $query;
    }
    
    function changeConfiguracionDieta(array $datos, int $id_usuario):bool{
        $query = $this->getQuery($datos);
        $queryProcesada = implode(',', $query);
        $statement = $this->pdo->prepare('UPDATE info_usuarios SET ('.$queryProcesada.') WHERE id_usuario=?');
        
    }
}

