<?php 
include_once("../config.system.php");
include_once(LIB_PATH . "responseRequest.php");
include_once(LIB_PATH . "router/router.php");

$Router = new Router();

include_once('./rutasPublicas.php');

// Validar session

// $isAuth = "";
// if ($isAuth == false) {
//   responseRequest(401, "Sin acceso", true);
// }

// Validar en la DB
$_SESSION['dataUser']['rol'] = 1;

include_once('./rutas.php');
