<?php

//use function PHPSTORM_META\type;

class Database
{
    public $status;
    private $connection;
    protected function __construct()
    {
        $string = "mysql:hostname=" . DBHOST . ";dbname=" . DBNAME;
        $con = new PDO($string, DBUSER, DBPASS);
        $con->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $con->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->connection = $con;
    }
    public function query($query, $data = []) {
        $connection = $this->connection;
        $this->status->query = $query;
        $this->status->data = $data;
        foreach ($data as $key => $value) {
            
        }
        //showd($query);
        try {
            $statement = $connection->prepare($query);
            $success = $statement->execute($data);
            $this->status->success = $success;
            $this->status->affected_rows = 0;
            if ($success) {
                $this->status->affected_rows = $statement->rowCount();
                $this->status->last_insert_id = $connection->lastInsertId();
                $this->status->result = $statement->fetchAll();
                return $this->status->result;
            } else {
                $this->status->error_code = $connection->errorCode();
                $this->status->error_info = $connection->errorInfo();
                return false;
            }
        } catch (PDOException $e) {
            $this->status->exception = $e;
            return false;
        }
    }
    
}
