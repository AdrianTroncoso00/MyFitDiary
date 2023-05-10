<?php


namespace App\Core;

use \PDO;

class DBManager {

    // Contenedor de la instancia de la Clase
    private static $instance;
    private $db;

    //Previene creacion de objetos via new

    private function __construct() {}

    // Única forma para obtener el objeto singleton

    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection($emulatePrepares = false) {
        if (is_null($this->db)) {
            $host = $_ENV['database.default.hostname'];
            $db = $_ENV['database.default.database'];
            $user = $_ENV['database.default.username'];
            $pass = $_ENV['database.default.password'];
            $charset = $_ENV['database.default.charset'];
            $emulated = (bool)$_ENV['database.default.emulated'];
            $port= $_ENV['database.default.port'];
            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => $emulated,
            ];
            try {
                $this->db = new PDO($dsn, $user, $pass, $options);
            } catch(\PDOException $e){
                throw new \PDOException($e->getMessage(), (int)$e->getCode());
            }
        }
        return $this->db;
    }

}

