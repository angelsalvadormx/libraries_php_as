<?php

function responseRequest($code, $message, $finishConnection = false, $data = [], $error = null)
{
  $arr = [];
  if (is_null($error)) {
    $arr["status"] = $code;
  } else {
    $arr["error"] = $error;
  }

  echo json_encode(array_merge($arr, ["message" => $message, "data" => $data]));
  if ($finishConnection) {
    die();
  }
}
