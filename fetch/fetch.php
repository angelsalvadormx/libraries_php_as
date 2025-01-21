<?php

/**
 * @param string $url
 * @param array $headers
 * @param string $method
 * 
 * @return Object
 */
function Fetch(string $url, string $method, array $headers, array $body = [])
{
    array_push($headers,"cache-control: no-cache");

    $request = array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $method,        
        CURLOPT_HTTPHEADER => $headers,
    );

    if($method == 'POST'){
        $request[10015] = json_encode($body);
    }
    $curl = curl_init();
    curl_setopt_array($curl,$request);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    if($response == false || empty($response)){
        $err = true;
        $response = '[]';
    }
    curl_close($curl);
    
    return (object) array(
        "data" => json_decode($response),
        "error" => $err
    );
}
