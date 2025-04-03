<?php

class PrepareData
{

  public function is_valid(array $rules, object $data): mixed
  {
    $errors = [];

    if(count((array) $data) == 0){
      $errors[] = "Datos vacios";
      return $errors;
    }
    foreach ($rules as $campo => $reglas) {
      $valor = property_exists($data, $campo) ? $data->$campo : null;
      $reglas_array = explode('|', $reglas);

      foreach ($reglas_array as $regla) {

        // Verifica si es requerido
        if ($regla === 'required' && (is_null($valor) || $valor === '')) {
          $errors[] = "Campo $campo es requerido";
          break;
        }

        if (is_null($valor)) {
          continue;
        }

        // Verifica el tipo de dato
        if ($regla === 'int' && !is_int($valor)) {
          $errors[] = "Campo $campo debe ser int";
          break;
        }
        if ($regla === 'string' && !is_string($valor)) {
          $errors[] = "Campo $campo debe ser string";
          break;
        }
        if ($regla === 'bool' && !is_bool($valor)) {
          $errors[] = "Campo $campo debe ser bool";
          break;
        }

        if ($regla === 'float') {
          // Validar si el valor es un float o una cadena que representa un float
          if (!filter_var($valor, FILTER_VALIDATE_FLOAT)) {
              $errors[] = "Campo $campo debe ser float";
          }
          break;
      }
      
        // Verifica la longitud máxima
        if (strpos($regla, 'max_length') !== false) {
          $max_length = explode(':', $regla)[1];
          if (is_string($valor) && strlen($valor) > $max_length) {
            $errors[] = "Campo $campo debe tener un máximo de $max_length caracteres";
            break;
          }
        }
      }

      // Limpia los valores para prevenir inyección de SQL
      if (is_string($valor)) {
        $data->$campo = htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
      }
    }

    return empty($errors) ? true : $errors;
  }
}
