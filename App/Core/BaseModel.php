<?php

namespace App\Core;
use App\Core\DBManager;
use \PDO;
abstract class BaseModel{
    protected $pdo;
    
    function __construct() {
        $this->pdo = DBManager::getInstance()->getConnection(); 
    }
            
}

