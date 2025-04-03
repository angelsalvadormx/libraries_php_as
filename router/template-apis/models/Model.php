<?php
require_once(LIB_PATH . 'mysql.class.php');

class Model
{

  protected $con;
  protected $tableName;
  protected $select;

  public function __construct($tableName = '')
  {
    $this->connectDB();
    $this->tableName = $tableName;
  }

  private function connectDB()
  {
    $this->con = new HelperMySql(DB_HOST_NAME, DB_USER_NAME, DB_PASSWORD, DB_NAME);
  }

  private function closeConnection()
  {
    if ($this->con) {
      $this->con->close();
      $this->con = null; 
    }
  }

  public function __destruct()
  {
    $this->closeConnection();
  }

  // public function get($where = '', $select = '*')
  // {
  //   $sql = "SELECT $select FROM $this->tableName ";
  //   if (!empty($where)) {
  //     $sql .= " WHERE " . $where;
  //   }
  //   $this->con->query($sql);
  //   return $this->con->fetch();
  // }

  // public function all($where = '', $select = '*', $orderBy = '')
  // {
  //   $sql = "SELECT $select FROM $this->tableName ";
  //   if (!empty($where)) {
  //     $sql .= " WHERE " . $where;
  //   }

  //   if (!empty($orderBy)) {
  //     $sql .= " ORDER BY $orderBy";
  //   }
  //   $this->con->query($sql);
  //   return $this->con->fetch_all();
  // }

  // public function remove($where)
  // {
  //   if (!empty($where)) {
  //     return false;
  //   }
  //   $sql = "DELETE FROM $this->tableName WHERE $where";
  //   $this->con->query($sql);
  //   return $this->con->affected_rows();
  // }
}
