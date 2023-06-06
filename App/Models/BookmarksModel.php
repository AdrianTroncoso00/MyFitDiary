<?php

namespace App\Models;


class BookmarksModel extends \CodeIgniter\Model {
    
    protected $table = 'bookmarks';
    protected $primaryKey = 'id_receta_fav';
    protected $allowedFields = ['id_usuario','image','label','url','calories','totalTime', 'dietlabels','cuisinetype','ingredientlines'];
    
    function getAllBookmarks(int $id_usuario):?array{
        return $this->asArray()->where(['id_usuario' => $id_usuario])->findAll();
    }
     
    function existeBookmark(string $label, int $id_usuario):bool{
        $bookmarks = $this->asArray()->where(['id_usuario' => $id_usuario, 'label'=>$label])->first();
        return !is_null($bookmarks) ? true : false;
    }

}

