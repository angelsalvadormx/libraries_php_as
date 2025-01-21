<?php
class Sqlsrv
{

    private ?object $con = null;
    private $result = false;
    private $stmt;
    private array $dataConexion = array();
    private string $host;
    private string $user;
    private string $password;
    private string $database;
    
    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $database
     */
    public function __construct(string $host, string $user, string $password, string $database){
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;

        if(defined('CON_DB')){
            $this->con = CON_DB;
        }else{
            $this->con = new PDO("sqlsrv:Server=$host;Database=$database", $user, $password);
            define('CON_DB',$this->con);
        }
    }

    /**
     * @param string $sql
     * 
     * @return Object
     */
    public function query(string $sql){
        $this->stmt = false;
        $this->result = $this->con->query($sql);
        return $this->result;        
    }

    private function reconect(){
        $this->con = new PDO("sqlsrv:Server=$this->host;Database=$this->database", $this->user, $this->password);
    }

    public function insert(string $sql, array $data){
        if(is_null($this->con)){
            $this->reconect();
        }
        $this->stmt = $this->con->prepare($sql);
        $this->stmt->execute($data);  
    }

    public function update(string $sql, array $data){
        $this->result = false;
        if(is_null($this->con)){
            $this->reconect();
        }
        $this->stmt = $this->con->prepare($sql);
        $this->stmt->execute($data);  
    }

    public function lastInsertId():mixed{
        return $this->con->lastInsertId();
    }

    public function fetch_all(){
        if($this->result === false){
            return false;
        }

        return $this->result->fetchall(PDO::FETCH_OBJ);
    }

    public function fetch_all_array(){
        if($this->result === false){
            return false;
        }
        return $this->result->fetchall(PDO::FETCH_ASSOC);
    }
    


    public function fetch_Object(){
        if($this->result === false){
            return (object) [];
        }
        $response = $this->result->fetchObject();
        return $response === false ? (object) [] : $response;
    }

    public function rowCount(): mixed{
        if($this->result !== false){
            return $this->result->rowCount();
        }

        if($this->stmt !== false){
            return $this->stmt->rowCount();
        }
        return 0;
    }

    public function error(): object{
        return (object) array(
            "ErrorCode" => $this->con->errorCode(),
            "ErrorMessage" => $this->con->errorInfo()
        );
    }

    public function close(){
        // Close the connection.
        unset($this->con);
    }

}
