<?php

include_once(LIB_PATH . "prepareData.class.php");
include_once(MODEL_PATH . "Model.php");
include_once(CONTROLLER_PATH . "controller.php");
include_once(CONSTANS_PATH . "typeUsers.php");


$Router->get("/alumnos", function ($req, $res) {
  include_once(CONTROLLER_PATH . "alumnos.controller.php");
  $alumno = new AlumnosController();

  $res->response(200, "Success", $alumno->getAll());
}, [isAdmin()]); // Middleware, si el usuario es admin puede acceder a esta ruta

$Router->get("/alumnos", function ($req, $res) {
  include_once(CONTROLLER_PATH . "alumnos.controller.php");
  $alumno = new AlumnosController();

  $res->response(200, "Success 2");
}, [isUser()]); // Middleware, si el usuario es usuario puede acceder a esta ruta



$Router->get("/alumno/:noCuenta", function ($req, $res) {
  include_once(CONTROLLER_PATH . "alumnos.controller.php");
  $alumno = new AlumnosController();

  $res->response(200, "Success", $alumno->getByNoCuenta($req->params->noCuenta));
}, []); // Middleware, no tiene filtros por rol

$Router->get("/alumno/nombre/:name", function ($req, $res) {
  include_once(CONTROLLER_PATH . "alumnos.controller.php");
  $alumno = new AlumnosController();

  $dataAlumno = $alumno->getByName($req->params->name);

  if (count((array) $dataAlumno) > 0) {
    $res->response(200, "success", $dataAlumno);
  }
  $res->response(404, "No existe");
}, []);


$Router->post("/alumno", function ($req, $res) {
  include_once(CONTROLLER_PATH . "alumnos.controller.php");
  $alumno = new AlumnosController();
  $response = $alumno->Create($req->body);
  $res->response($response->statusCode, $response->message, $response->data);
});

$Router->put("/alumno", function ($req, $res) {
  include_once(CONTROLLER_PATH . "alumnos.controller.php");
  $alumno = new AlumnosController();
  $response = $alumno->edit($req->body);
  $res->response($response->statusCode, $response->message, $response->data);
});

$Router->delete("/alumno/:idAlumno", function ($req, $res) {
  include_once(CONTROLLER_PATH . "alumnos.controller.php");
  $alumno = new AlumnosController();

  $response = $alumno->remove($req->params->idAlumno);
  $res->response($response->statusCode, $response->message, $response->data);
});

$Router->default(function ($req, $res) {
  $res->response($req->statusCode, $req->message);
});
