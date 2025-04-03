<?php

class Alumnos extends Model
{


  public function __construct()
  {
    parent::__construct("alumnos");
  }

  public function getAllAlumnos(): array
  {
    $sql = "SELECT * FROM $this->tableName";
    $this->con->query($sql);
    return $this->con->fetch_all();
  }

  public function getAlumno(string $where = "", string $select = "*"): object
  {
    $sql = "SELECT $select FROM $this->tableName";
    if (!empty($where)) {
      $sql .= " WHERE $where";
    }
    $this->con->query($sql);
    return $this->con->fetch();
  }

  public function insert(array $data): mixed
  {
    $sql = "INSERT INTO $this->tableName (nombre,no_cuenta,edad) VALUES (?,?,?)";
    $this->con->insert($sql, "ssi", $data);
    return $this->con->last_id();
  }

  public function update(array $data): int
  {
    $sql = "UPDATE $this->tableName SET nombre = ?, no_cuenta =?, edad = ? WHERE id_alumno = ?";
    $this->con->update($sql, "ssii", $data);
    return $this->con->affected_rows();
  }

  public function delete(string $where):bool{
    if(empty($where)){
      return false;
    }
    $sql = "DELETE FROM $this->tableName WHERE $where";
    $this->con->query($sql);
    return $this->con->affected_rows() > 0;
  }
}
