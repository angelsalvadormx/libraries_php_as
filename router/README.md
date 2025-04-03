# Template Router

Nota: htaccess no funciona en servidores nginx,

#### Ruta en servidor apache 
http://localhost/api/v1/test

#### Ruta en servidor nginx
http://localhost/api/index.php/v1/test

### Todas las rutas deben comenzar con un /, ejemplo /test
```php
$Router->get('/test', function () {
```

### El callback de las ruta debe tener un response 
```php
$Router->get("/alumnos", function ($req, $res) {
  // incluir el controlador
  include_once(CONTROLLER_PATH . "alumnos.controller.php");
  // Crear instancia de la clase
  $alumno = new AlumnosController();
  // mandar respuesta
  $res->response(200, "Success 2");
});
```

### Si una ruta no se encuentra, existe el metodo default
Esta debe ir despues de la ultima ruta agregada
```php
$Router->default(function ($req, $res) {
  $res->response($req->statusCode, $req->message);
});
```
#### Ejemplo

```php
$Router->get('/test', function () {
  responseRequest(200, 'succces', true, []);
});

$Router->default(function ($req, $res) {
  $res->response($req->statusCode, $req->message);
});
```

## Metodos http existentes
 GET 
```php
$Router->get('/test', function () {
```
 POST 
```php
$Router->post('/test', function ($req) {
  // Obtener el body
  $body = $req->body;

});
```
 PUT 
```php
$Router->put('/test', function ($req) {
  // Obtener el body
  $body = $req->body;

});
  
```
 DELETE 
```php
$Router->delete('/test/:id_test', function ($req) {
  $idTest = $req->params->id_test;
});
```

## GET con parametros

```php
// v1/test/450
$Router->get('/test/:id_test', function ($req) {
  $idTest = $req->params->id_test;
});

// v1/test/465/events/85
$Router->get('/test/:id_test/events/:id_event', function ($req) {
  $idTest = $req->params->id_test;
  $idEvent = $req->params->id_event;

});
```

## PUT con parametros

```php
// v1/test/524
$Router->put('/test/:id_test', function ($req) {
  $idTest = $req->params->id_test;
  $body = $req->body;

});
```

## Manejo de versiones
Por default se tiene la version 1 (v1).

#### Ejemplo api version 1
 http://localhost/api/v1/test 


```php
// v1/test
$Router->get('/test',function(){

// Cambiar version de rutas
$Router->setRouteVersion('v2');

// v2/test
$Router->get('/test',function(){

```
Todas las rutas que se escriban despues de esa linea de codigo seran version 2

#### Ejemplo api version 2
 http://localhost/api/v2/test 


### Config (servidor apache)
 ```
RewriteEngine On

# Router Lib
RewriteRule ^([a-zA-Z0-9\/+]+)$  index.php?path=$1 [L,NC]

 ```
