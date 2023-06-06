<?php

namespace App\Controllers;

class BookmarksController extends \App\Core\BaseController {
    
    function showBookmarks(){
        $modelo = new \App\Models\BookmarksModel();
        var_dump($_SESSION);
        $bookmarks =$modelo->getAllBookmarks($_SESSION['usuario']['id']);
        if(!is_null($bookmarks)){
            $data['recetas'] = $this->modifyBookmarksArray($modelo->paginate(8));
            $data['pager']=$modelo->pager;
            return view('left-menu.view.php').view('bookmark.view.php',$data);
        }else{
            return redirect()->to('/favoritos');
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
            $modelo->save($data);
            session()->setFlashdata('alert', 'alert-success');
            session()->setFlashdata('msg', 'Receta guardada correctamente');
            return redirect()->to('/favoritos'); 
        }else{
            $_SESSION['status']='Esta receta ya se encuentra en favoritos';
            return redirect()->to('/favoritos'); 
        }
    }
//    function addBookmark(){
//        $modelo = new \App\Models\BookmarksModel();  
//        var_dump($_POST);
//        if(!$modelo->existeBookmark($_POST['label'], $_SESSION['usuario']['id'])){
//            if($modelo->addRecetaFavorita($_SESSION['usuario']['id'], $_POST)){
//                return redirect()->to($_SERVER['HTTP_REFERER']);
//            }else{
//                $data['error']='Ha ocurrido un error indeterminado al guardar, vuelve a intentarlo mas tarde';
//                return view('left-menu.view.php'). view('recipe-search-results.view.php',$data); 
//            }
//        }else{
//            $_SESSION['error']='La receta ya se encuentra en favoritos';
//            return redirect()->to($_SERVER['HTTP_REFERER']);
//            
//        }
//    }
    
    
//    function deleteBookmark(int $id_receta){
//        $modelo = new \App\Models\BookmarksModel();  
//        if($modelo->deleteRecetaFavorita($id_receta)){
//            return redirect()->to('/favoritos');
//        }else{
//            $data=[];
//            return view('left-menu.view.php'). view('recipe-search-results.view.php',$data); 
//        }
//    }
    function deleteBookmark(int $id_receta){
        $modelo = new \App\Models\BookmarksModel();  
        if($modelo->delete($id_receta)){
            return redirect()->to('favoritos');
        }
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
