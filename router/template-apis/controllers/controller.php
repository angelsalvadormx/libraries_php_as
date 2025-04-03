<?php

class Controller
{


  public function __construct() {}

  protected function response($error, $code, $msg, $data = [])
  {
    return (object) [
      "error" => $error,
      "statusCode" => $code,
      "message" => $msg,
      "data" => $data
    ];
  }

  protected function orderObject($object, $arrayOrder)
  {
    $orderedObject = new stdClass();

    foreach ($arrayOrder as $key) {
      if (property_exists($object, $key)) {
        $orderedObject->{$key} = $object->{$key};
      }
    }

    return $orderedObject;
  }
}
