<?php

class Response
{
    public function __construct()
    {
    }
    
    public function response($code, $message, $data = [], $error = null)
    {
        $arr = [];
        if (is_null($error)) {
            $arr["status"] = $code;
        } else {
            $arr["error"] = $error;
        }

        echo json_encode(array_merge($arr, ["message" => $message, "data" => $data]));
        die();
    }

    public function json($object)
    {
        echo json_encode($object);
        die();
    }
}
