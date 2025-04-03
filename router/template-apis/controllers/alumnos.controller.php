<?php
include_once(MODEL_PATH . "Alumnos.php");
include_once(CONSTANS_PATH . "constants.alumnos.php");

class AlumnosController extends Controller
{

  private object $alumno;
  private object $validateClass;

  public function __construct()
  {
    $this->alumno = new Alumnos();
    parent::__construct();
  }

  public function getAll(): array
  {
    return $this->alumno->getAllAlumnos();
  }

  public function getByNoCuenta(int $cuenta): object
  {
    return $this->alumno->getAlumno("no_cuenta = $cuenta", "nombre, edad");
  }

  public function getByName(string $name): object
  {
    return $this->alumno->getAlumno("nombre like '%$name%'");
  }

  public function Create(object $data): object
  {
    $this->validateClass = new PrepareData();

    // RULES_CREATE_ALUMNOS from constants.alumnos.php
    $isValid = $this->validateClass->is_valid(RULES_CREATE_ALUMNOS, $data);

    if (is_array($isValid)) {
      return $this->response(true, 400, "Error en los campos", $isValid);
    }

    $found = $this->exists($data);
    if (count((array) $found) > 0) {
      return $this->response(true, 200, "ya existe el alumno", $found);
    }

    $order = array_keys(RULES_CREATE_ALUMNOS);
    $newData = $this->orderObject($data, $order);

    $idInserted = $this->alumno->insert((array) $newData);
    if (is_null($idInserted)) {
      return $this->response(true, 400, "Error al insertar");
    }
    return $this->response(false, 201, "creado");
  }

  public function edit(object $data): object
  {
    $this->validateClass = new PrepareData();

    // RULES_UPDATE_ALUMNOS from constants.alumnos.php
    $isValid = $this->validateClass->is_valid(RULES_UPDATE_ALUMNOS, $data);
    if (is_array($isValid)) {
      return $this->response(true, 400, "Error en los campos", $isValid);
    }
    
    // Validar if exists 
    $found = $this->exists($data);
    if (count((array) $found) == 0) {
      return $this->response(true, 404, "No existe el alumno");
    }
    
    
    // RULES_UPDATE_ALUMNOS from constants.alumnos.php
    $order = array_keys(RULES_UPDATE_ALUMNOS);
    $newData = $this->orderObject($data, $order);

    $totalUpdated = $this->alumno->update((array) $newData);
    if ($totalUpdated > 0) {
      return $this->response(false, 200, "Actualizado");
    }
    return $this->response(true, 400, "No actualizado");
  }

  public function remove($id_alumno): object
  {

    // Create object to validate
    $data = (object)["id_alumno" => $id_alumno];
    $this->validateClass = new PrepareData();

    // RULES_DELETE_ALUMNOS from constants.alumnos.php
    $isValid = $this->validateClass->is_valid(RULES_DELETE_ALUMNOS, $data);
    if (is_array($isValid)) {
      return $this->response(true, 400, "Error en los campos", $isValid);
    }


    $deleted = $this->alumno->delete("id_alumno = $data->id_alumno");
    if ($deleted) {
      return $this->response(false, 200, "Eliminado");
    }
    return $this->response(true, 400, "No eliminado");
  }
  
  public function exists(object $data): object
  {
    $where = isset($data->id_alumno) ? "id_alumno = $data->id_alumno" : "nombre = '$data->nombre'";
    return $this->alumno->getAlumno($where);
  }

  // public function getByName(string $name): object
  // {
  //   $this->alumno = new Alumnos();
  //   $found = $this->alumno->getAlumno("nombre like '%$name%'");
  //   if (count((array) $found) > 0) {
  //     return $this->response(false, 200, "success", $found);
  //   }
  //   return $this->response(true, 404, "No encontrado");
  // }


}
