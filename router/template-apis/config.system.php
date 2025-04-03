<?php

session_start();

header('Content-Type: application/json');


$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

// Permitir el control de acceso solo para el origen de la solicitud
header('Access-Control-Allow-Origin: ' . $origin);
// Permitir el envío de cookies en las solicitudes
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  die();
}

// Use PHP's directory separator for windows/unix compatibility
defined('DS') ? NULL : define('DS', DIRECTORY_SEPARATOR);
defined('SITE_ROOT') ? NULL : define('SITE_ROOT', dirname(__FILE__) . DS);
defined('MODEL_PATH') ? NULL : define('MODEL_PATH', SITE_ROOT . 'models' . DS);
defined('CONTROLLER_PATH') ? NULL : define('CONTROLLER_PATH', SITE_ROOT . 'controllers' . DS);
defined('LIB_PATH') ? NULL : define('LIB_PATH', SITE_ROOT . 'libraries' . DS);
defined('CONSTANS_PATH') ? NULL : define('CONSTANS_PATH', SITE_ROOT . 'constants' . DS);
defined('PUBLIC_DATA_PATH') ? NULL : define('PUBLIC_DATA_PATH', SITE_ROOT . 'data' . DS);

defined('DB_HOST_NAME') ? NULL : define('DB_HOST_NAME', getenv("ENDPOINT"));
defined('DB_USER_NAME') ? NULL : define('DB_USER_NAME', getenv("USERD"));
defined('DB_NAME') ? NULL : define('DB_NAME', getenv("DATABASE"));
defined('DB_PASSWORD') ? NULL : define('DB_PASSWORD', getenv("PASSD"));
