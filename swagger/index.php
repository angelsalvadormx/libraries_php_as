<?php
// Ruta al archivo swagger.yaml
$swaggerFile = 'swagger.yaml';

// Lee el contenido del archivo swagger.yaml
$swaggerContent = file_get_contents($swaggerFile);

// Ruta al archivo JSON de Postman
$jsonFile = 'postman.json';
$jsonContent = file_get_contents($jsonFile);
$jsonContentEscaped = json_encode(json_decode($jsonContent), JSON_UNESCAPED_SLASHES);

// HTML y JavaScript para mostrar Swagger UI
echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Swagger UI</title>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@3/swagger-ui.css">
    <script src="https://unpkg.com/swagger-ui-dist@3/swagger-ui-bundle.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-yaml/4.1.0/js-yaml.min.js"></script>
    <script src="./script.js"></script>

</head>
<body>
    <div id="swagger-ui"></div>
    <script>
        window.onload = function() {


            // Ejemplo de uso:
            const postmanJSON = $jsonContentEscaped;
            // console.log(postmanJSON);
            const yml  = convertPostmanToYaml(postmanJSON);
            console.log('yml',yml);
            var spec = jsyaml.load(yml);
            SwaggerUIBundle({
                spec: spec,
                dom_id: '#swagger-ui',
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIBundle.SwaggerUIStandalonePreset
                ],
                layout: "BaseLayout"
            });

        }



        
    </script>
</body>
</html>
HTML;
