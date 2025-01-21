<?php
function validate_data($rules, $data)
{
  $errors = [];

  foreach ($rules as $campo => $reglas) {
    $valor = property_exists($data, $campo) ? $data->$campo : null;
    $reglas_array = explode('|', $reglas);
  
    foreach ($reglas_array as $regla) {

      // Verifica si es requerido
      if ($regla === 'required' && (is_null($valor) || $valor === '')) {
        $errors[] = "Campo $campo es requerido";
        break;
      }

      if(is_null($valor)){
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
      if ($regla === 'float' && !is_float($valor)) {
        $errors[] = "Campo $campo debe ser float";
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



function clear_remove_extra_fields( $rules, $data)
{
  $data_clean = new \stdClass();

  foreach ($rules as $campo) {
    $valor = property_exists($data, $campo) ? $data->$campo : null;

    // Limpia los valores para prevenir inyección de SQL
    if (is_string($valor)) {
      $valor = htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
    }

    // Solo agrega los campos definidos en las reglas al objeto limpio
    $data_clean->$campo = $valor;
  }

  return $data_clean;
}