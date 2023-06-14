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
                'ingredientlines' => $_POST['ingredientLines'] 
            ];
            return $modelo->save($data) ? redirect()->to('/favoritos')->with('good', 'Recipe saved successfully.') : redirect()->to('/favoritos')->with('bad', 'The recipe could not be saved to favorites.');
        }else{
            return redirect()->to('/favoritos')->with('bad', 'The recipe is already in favorites.'); 
        }
    }

    function deleteBookmark(int $id_receta){
        $modelo = new \App\Models\BookmarksModel();  
        return $modelo->delete($id_receta) ? redirect()->to('favoritos')->with('good', 'Recipe deleted successfully.') : redirect()->to('favoritos')->with('bad', 'The recipe could not be deleted.');
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

