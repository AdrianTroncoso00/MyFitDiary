<?php

namespace App\Controllers;

class BookmarksController extends \App\Core\BaseController {
    
    function showBookmarks(){
        $modelo = new \App\Models\BookmarksModel();
        $bookmarks = $modelo->getAllBookmarks($_SESSION['usuario']['id']);
        if(!empty($bookmarks)){
            $data['recetas'] = $this->modifyBookmarksArray($modelo->paginate(8));
            $data['pager']=$modelo->pager;
            return view('templates/left-menu.view.php').view('bookmark.view.php',$data).view('templates/footer.view.php');
        }else{
            $data['recetas']=[];
            return view('templates/left-menu.view.php').view('bookmark.view.php',$data).view('templates/footer.view.php');
        }
    }
    
    function addBookmark(){
        $modelo = new \App\Models\BookmarksModel();
        if(!$modelo->existeBookmark($_POST['label'], $_SESSION['usuario']['id'])){
            $data=[
                'id_usuario' => $_SESSION['usuario']['id'],
                'image' => $_POST['image'],
                'label' => $_POST['label'],
                'url' => $_POST['url'],
                'calories' => $_POST['calories'],
                'totalTime' => $_POST['totalTime'],
                'dietlabels' => $_POST['dietLabels'],
                'cuisinetype' => $_POST['cuisineType'],
                'ingredietnlines' => $_POST['ingredientLines'] 
            ];
            return $modelo->save($data) ? redirect()->to('/favoritos')->with('good', 'Receta guardada correctamente') : redirect()->to('/favoritos')->with('bad', 'No se ha podido guardar la receta a favoritos');
        }else{
            return redirect()->to('/favoritos')->with('bad', 'La receta ya se encuentra en favoritos'); 
        }
    }

    function deleteBookmark(int $id_receta){
        $modelo = new \App\Models\BookmarksModel();  
        return $modelo->delete($id_receta) ? redirect()->to('favoritos')->with('good', 'Receta eliminada correctamente') : redirect()->to('favoritos')->with('bad', 'No se ha podido borrar la receta');
    }
    
    function modifyBookmarksArray(array $bookmarks):array{
        foreach ($bookmarks as $key=>$fav) {
            $bookmarks[$key]['ingredientLines'] = explode(',', $fav['ingredientlines']);
            $bookmarks[$key]['dietLabels'] = explode(',', $fav['dietlabels']);
            unset($bookmarks[$key]['ingredientlines']);
            unset($bookmarks[$key]['dietlabels']);
        }
        return $bookmarks;
    }
}

