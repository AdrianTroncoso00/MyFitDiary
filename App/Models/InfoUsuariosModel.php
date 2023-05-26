<?php

namespace App\Models;

class InfoUsuariosModel extends \App\Core\BaseModel {
    
    function addInfoUsuario(array $datos, int $id_usuario, float $imc, array $calorias):bool{
        $statement = $this->pdo->prepare('INSERT INTO info_usuarios (genero, objetivo, edad, id_usuario, imc, metabolismo_basal, calorias_mantenimiento, calorias_objetivo, peso, estatura, actividad_fisica, nombre_completo, num_comidas, porcent_breakfast, porcent_brunch, porcent_lunch, porcent_snack, porcent_dinner) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $statement->execute([$datos['genero'], $datos['objetivo'], $datos['edad'], $id_usuario, $imc, $calorias['tmb'], $calorias['caloriasMantenimiento'], $calorias['caloriasObjetivo'], $datos['peso'], $datos['altura'], $datos['actividad'], $datos['nombre'], $datos['num_comidas'], $datos['porcent_breakfast'], $datos['porcent_brunch'], $datos['porcent_lunch'], $datos['porcent_snack'], $datos['porcent_dinner']]);
        return $statement->rowCount()==1;
    }
}

