<?php

class Model extends Database
{

    protected   $limit        = 50;
    protected   $offset       = 0;
    protected   $order_type   = "desc";
    protected   $order_column = "id";
    public $errors = [];

    function __construct($table = '')
    {
        parent::__construct();
        $this->status = new stdClass;
        if ($table === '') {

            if (!property_exists($this, 'table')) {
                $this->table = strtolower($this::class);
            }
        } else $this->table = $table;
    }

    public function setLimit($limit = 10)
    {
        $this->limit = $limit;
    }
    public function setOffset($offset = 0)
    {
        $this->offset = $offset;
    }

    public function findAll()
    {

        $query = " select * from $this->table limit $this->limit offset $this->offset";

        return $this->query($query);
    }
    public function where($data, $s = '*', $data_not = [])
    {
        $keys = array_keys($data);
        $keys_not = array_keys($data_not);
        $query = "select $s from $this->table where ";

        foreach ($keys as $key) {
            $query .= $key . " = :" . $key . " && ";
        }
        foreach ($keys_not as $key) {
            $query .= $key . " != :" . $key . " && ";
        }

        $query = trim($query, " && ");

        $query .= " order by $this->order_column $this->order_type limit $this->limit offset $this->offset";

        $data = array_merge($data, $data_not);

        //show( $this->query($query, $data));

        return $this->query($query, $data);
    }
    public function count_($data, $s = 'count(1)')
    {
        $keys = array_keys($data);
        $query = "select $s from $this->table where ";

        foreach ($keys as $key) {
            $query .= $key . " = :" . $key . " && ";
        }

        $query = trim($query, " && ");

        return $this->query($query, $data)['0'][$s];
    }
    /* this last method should be replaced */
    public function last($data, $data_not = [])
    {
        $keys = array_keys($data);
        $keys_not = array_keys($data_not);
        $query = "select * from $this->table where ";

        foreach ($keys as $key) {
            $query .= $key . " = :" . $key . " && ";
        }
        foreach ($keys_not as $key) {
            $query .= $key . " != :" . $key . " && ";
        }

        $query = trim($query, " && ");

        $query .= " order by $this->order_column desc limit $this->limit offset $this->offset";

        $data = array_merge($data, $data_not);
        $result = $this->query($query, $data);
        if ($result) return $result[0];
        return false;
    }

    public function insert($data)
    {
       

        $keys = array_keys($data);
        $query = "insert into $this->table (" . implode(",", $keys) . ") values (:" . implode(",:", $keys) . ")";
        if ($this->query($query, $data)) return true;
        return true;
    }

    public function update($id, $data, $id_column = 'id')
    {
        /* removing unwanted data */
        if (!empty($this->allowedColumns)) {
            foreach ($this->allowedColumns as $key => $value) {
                if (!in_array($key, $this->allowedColumns)) {
                    unset($data[$key]);
                }
            }
        }

        $keys = array_keys($data);
        $query = "update $this->table set  ";

        foreach ($keys as $key) {
            $query .= $key . " = :" . $key . ", ";
        }


        $query = trim($query, ", ");

        $query .= " where $id_column = :$id_column";

        $data[$id_column] = $id;

        return $this->query($query, $data);
    }

    public function delete($id, $id_column = 'id')
    {
        $data[$id_column] = $id;
        $query = "delete from $this->table where $id_column = :$id_column";
        return $this->query($query, $data);
    }
    public function lastInsertId($s = 'id')
    {
        $query = "select $s from $this->table ORDER BY id DESC LIMIT 1";

        return $this->query($query)[0][$s];
    }
    public function count($s = 'id')
    {
        //'count(id)')['0']['count(id)']
        $query = "select count($s) from $this->table";

        return $this->query($query);
    }


    /**  */
    public function available_Spots_Per_Day($data = []){
        
      
        
        $query = "SELECT
        (
            SELECT available_hours
            FROM horaires
            WHERE jour = :jour
        ) - (
            SELECT
            COUNT(id)
            FROM Rendez_vous
            WHERE date_jour = :date_jour
        ) AS available_hours";

        return $this->query($query,$data);
    }

    public function reserved_Spots_Per_Day($data){
        unset($data['jour']);
        $query = "
        SELECT heure 
        FROM Rendez_vous
        WHERE date_jour = :date_jour
        ";
        $data = $this->query($query, $data);
        $response = [];

        foreach ($data as $key => $value) {
            $response[$key] = $value['heure'];
        }
        return $response;
    }

    public function working_houres($data){
        unset($data['date_jour']);
        $query = "
        SELECT ouverture_matain, fermeture_matain, ouverture_midi, fermeture_midi 
        FROM horaires
        WHERE jour = :jour
        ";
        return $this->query($query, $data);
    }

    public function pending($data){
        
        $query = "
        select count(identifiant_utilisateur) as count from Rendez_vous where date_jour >= DATE_ADD(CURDATE(), INTERVAL 0 DAY) && identifiant_utilisateur = :identifiant_utilisateur
        ";
        return $this->query($query,$data)[0]['count'];
    }
}
