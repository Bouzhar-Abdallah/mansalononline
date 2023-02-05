<?php
class Database{
    private $hostname = 'localhost' ;
    private $user = 'root';
    private $password = 'root';
    private $DbName = 'MonSalonline';

    private $connection;
    private $stmt;

    function __construct()
    {
        $dsn = 'mysql:host='.$this->hostname.';dbname='.$this->DbName;
        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );
        try {
            $this->connection = new PDO($dsn,$this->user,$this->password,$options);
        } catch (PDOException $th) {
            $exception = $th->getMessage();
            var_dump($exception) ;
        }
    }

    public function query(){
        $query = "insert into utilisateur (identifiant, nom, prenom, numero_tel) values ('test', 'bouzhar', 'abdallah', '0649600623')";

        try {
            $this->connection->query($query);
        } catch (PDOException $th) {
            $exception = $th->getMessage();
            var_dump($exception);
        }
    }

}

$db = new Database;

$db->query();