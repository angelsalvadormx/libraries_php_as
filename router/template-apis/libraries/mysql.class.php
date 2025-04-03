<?php
/*
	 * Clase que sirve para hacer la conexion a la base de datos, realizar consultas y obtener los resultados de la consulta
	 * usando mysqli
	*/
class HelperMySql
{
  private $M = null; //Object MySQLi Connection
  private $sentence = null;
  private $R; //Object Result
  private $data = array(); //Datos de conexi�n

  public function __construct($h, $u, $p, $d)
  {
    /* guardamos datos de conexi�n por si se
			 * llega a perder conexi�n entonces reconectar */
    $this->data = array($h, $u, $p, $d);
    $this->sentence = null;

    if (is_null($this->M)) {
      $this->reconect();
    }
  }

  private function reconect()
  {
    // if (defined('CON_DB')) {
    // $this->M = CON_DB;
    // } else {
    /* realizamos la conexi�n */
    $this->sentence = null;
    $this->M = new mysqli($this->data[0], $this->data[1], $this->data[2], $this->data[3]);
    mysqli_set_charset($this->M, "utf8");
    // define('CON_DB', $this->M);
    // }
  }

  /* elimina la conexion una vez terminado el proceso.	*/
  public function __destruct()
  {
    $this->close();
  }

  public function free_result()
  {
    $this->R->close();
  }

  public function insert($sql, $types, $values)
  {
    $sentencia = $this->M->prepare($sql);

    $this->bindValues($sentencia, $types, $values);

    // Ejecuta la sentencia
    $sentencia->execute();
  }

  public function update($sql, $types, $values)
  {
    $sentencia = $this->M->prepare($sql);

    $this->bindValues($sentencia, $types, $values);

    // Ejecuta la sentencia
    $sentencia->execute();

    $this->sentence = $sentencia; 
    
  }

  private function bindValues($stmt, $types, $values)
  {
    // Prepara un array con referencias a los valores
    $refs = array();
    $refs[] = $types; // Agrega el tipo de datos al principio del array
    foreach ($values as &$value) {
      $refs[] = &$value; // Agrega referencia a cada valor al array
    }
    // Llama a bind_param utilizando call_user_func_array
    call_user_func_array(array($stmt, 'bind_param'), $refs);
  }


  /* 
		 * ejecuta una consulta SQL y asigna el Object Result a la
		 * variable interna y privada $R.
		 */
  public function query($sql)
  {
    $this->R = false;
    /* Si la conexi�n se ha perdido (MySQL server
			 * has gone away o parecidos), reconectamos al servidor. */
    if ($this->M->errno) {
      $this->__construct($this->data[0], $this->data[1], $this->data[2], $this->data[3]);
    }


    $this->R = $this->M->query($sql);

    if ($this->M->errno) {
      $this->R = false;
      return false;
    } else {
      return $this->R;
    }
  }


  /* 
		 * ejecuta una consulta SQL y asigna el Object Result a la
		 * variable interna y privada $R.
		 */
  public function multi_query($sql)
  {
    $this->R = false;
    /* Si la conexi�n se ha perdido (MySQL server
			 * has gone away o parecidos), reconectamos al servidor. */
    if ($this->M->errno) {
      $this->__construct($this->data[0], $this->data[1], $this->data[2], $this->data[3]);
    }

    //$this->debug($sql);

    $this->R = $this->M->multi_query($sql);

    if ($this->M->errno) {
      $this->R = false;
      return false;
    } else {
      return $this->R;
    }
  }

  public function debug($str, $exit = false)
  {
    return false;
    echo "<pre style='background-color:#fff;font-size:14px;font-family:tahoma;padding:10px;border-bottom:1px solid #000;'>";
    print_r($str);
    echo "</pre>";

    if ($exit) exit();
  }


  /* 
		 * regresa un registro de la variable privada Object Result $R
		 */
  public function fetch($R = null)
  {
    if (is_null($R)) {
      $R = $this->R;
    }

    if ($R == false) {
      $result = (object) [];
    } else {
      $result = (object) $R->fetch_assoc();
    }

    return $result;
  }

  /* 
		 * regresa un registro de la variable privada Object Result $R
		 */
  public function fetch_array($R = null)
  {
    if (is_null($R)) {
      $R = $this->R;
    }

    if ($R == false) {
      $result = false;
    } else {
      $result = $R->fetch_array();
    }

    return $result;
  }
  /* 
		 * regresa un registro de la variable privada Object Result $R
		 */
  public function fetch_fields($R = null)
  {
    if (is_null($R)) {
      $R = $this->R;
    }

    if ($R == false) {
      $result = false;
    } else {
      $result = $R->fetch_fields();
    }

    return $result;
  }

  /*
		 * Cierra la conexi�n MySQLi si es que no se ha cerrado ya.
		 */
  public function close()
  {
    /* Si la conexi�n no se ha perdido entonces la cerramos */
    if (isset($this->M->errno) && $this->M->errno == 0) {
      $this->M->close();
    }

    return true;
  }

  public function next_result()
  {
    /* Si la conexi�n no se ha perdido entonces la cerramos */
    if ($this->M->errno == 0) {
      $this->M->next_result();
    }

    return true;
  }

  public function escape($str)
  {
    /* Si la conexi�n se ha perdido (MySQL server
			 * has gone away o parecidos), reconectamos al servidor. */
    if ($this->M->errno) {
      $this->__construct($this->data[0], $this->data[1], $this->data[2], $this->data[3]);
    }

    return $this->M->real_escape_string($str);
  }

  /*
		 * regresa el �ltimo id insertado en un registro.
		 */
  public function last_id()
  {
    if ($this->M->errno === 0) {
      return $this->M->insert_id;
    } else {
      return false;
    }
  }

  /*
		 * regresa la cantidad de registros afectados por un 
		 * INSERT, UPDATE, DELETE
		 */
  public function affected_rows()
  {
    if(!is_null($this->sentence)){
      return $this->sentence->affected_rows;
    }
    return $this->M->affected_rows;
  }

  /*
		 * regresa la cantidad de registros traidos por un SELECT
		 */
  public function count_rows()
  {
    return $this->R->num_rows;
  }

  /*
		 * regresa la cantidad de registros traidos por un SELECT
		 */

  public function set_charset($charset)
  {
    return $this->M->set_charset($charset);
  }

  // Regresa un arreglo con TODOS los resultados
  public function fetch_all($resulttype = MYSQLI_ASSOC)
  {
    $R = $this->R;

    if (method_exists('mysqli_result', 'fetch_all')) # Compatibility layer with PHP < 5.3
      $res = $this->R->fetch_all($resulttype);
    else
      for ($res = array(); $tmp = $R->fetch_array($resulttype);) $res[] = $tmp;

    return $res;
  }

  /*
		 * regresa la cantidad de registros traidos por un SELECT
		 */
  public function error()
  {
    return $this->R->errno;
  }
}