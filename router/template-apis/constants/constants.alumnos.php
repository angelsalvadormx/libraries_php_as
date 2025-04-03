<?php

$baseFiels = [
  "nombre" => "required|string",
  "no_cuenta" => "required|string",
  "edad" => "required|int"
];

// Rutes create student
define("RULES_CREATE_ALUMNOS", $baseFiels);


// Rutes edit student
$fieldEdit = [
  "id_alumno" => "required|int"
];

define("RULES_UPDATE_ALUMNOS", array_merge($baseFiels, $fieldEdit));


define("RULES_DELETE_ALUMNOS", $fieldEdit);
