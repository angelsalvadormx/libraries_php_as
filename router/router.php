<?php

include_once('response.php');
class Router
{

    private  $routes = [];
    private  $version = 'v1';
    private  $params = [];
    private  $routeFound;
    private  $body;
    private  $hasAccess = true;


    public function __construct()
    {
        $this->routeFound = false;
    }

    public function setRouteVersion($version = 'v1')
    {
        $this->version = $version;
    }


    private function middlewares($middlewares)
    {
        if (count($middlewares) == 0) {
            return true;
        }
        $access = array_search(true, $middlewares);
        if ($access === false) {
            $this->routeFound = true;
            $this->hasAccess = false;
        }
        return $access !== false;
    }

    private function callbackFn($callback, $method)
    {
        $req['params'] = (object) $this->params;
        if (in_array($method, ["POST", "PUT"])) {
            $req['body'] = (object) $this->body;
        }
        $res = new Response();
        $callback((object) $req, $res);
    }

    public function get($URL, $callback, $middlewares = [])
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'GET') {
            return;
        }

        if ($this->executeThisURL($URL, $middlewares)) {
            $this->callbackFn($callback, $method);
            return;
        };
    }


    public function delete($URL, $callback, $middlewares = [])
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'DELETE') {
            return;
        }

        if ($this->executeThisURL($URL, $middlewares)) {
            $this->callbackFn($callback, $method);
            return;
        };
    }

    public function post($URL, $callback, $middlewares = [])
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            return;
        }

        if ($this->executeThisURL($URL, $middlewares)) {
            $this->callbackFn($callback, $method);
            return;
        }
    }

    public function put($URL, $callback, $middlewares = [])
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'PUT') {
            return;
        }

        if ($this->executeThisURL($URL, $middlewares)) {
            $this->callbackFn($callback, $method);
        }
    }

    public function default($callback)
    {
        $errorMsg = "";
        $errorCode = "";
        if (!$this->routeFound) {
            $errorMsg = "API not found";
            $errorCode = 404;
        }
        if (!$this->hasAccess) {
            $errorMsg = "Sin acceso";
            $errorCode = 403;
        }

        $res = new Response();
        $callback(
            (object) [
                "message" => $errorMsg,
                "statusCode" => $errorCode,
                "hasAccess" => $this->hasAccess
            ],
            $res
        );
    }

    private function executeThisURL($URL, $middlewares)
    {
        $accces = $this->middlewares($middlewares);

        if (!$accces) {
            return false;
        }

        $this->setRoute($URL);
        return $this->start();
    }
    public function setRoute($routeName)
    {
        $exist = $this->existsRoute($routeName);
        if ($exist !== false) {
            throw new Error("This URL: $routeName already exists");
        }
        $splitedRoutName = explode('/', $routeName);
        array_push($this->routes, (object) array(
            "url" => $this->version . $routeName,
        ));
    }

    private function existsRoute($routeName)
    {
        $newRoute = $this->version . $routeName;
        $found = array_search($newRoute, array_column($this->routes, 'url'));
        return $found;
    }

    private function hasSameParams($currentURLSplited, $routeUrlSplited)
    {
        $res = true;
        foreach ($routeUrlSplited as $key => $item) {
            if (strpos($item, ':') !== false) {
                $valuePram = $currentURLSplited[$key];
                $res = !empty($valuePram);
            }
        }
        return $res;
    }

    private function isSameURLString($currentURLSplited, $routeUrlSplited)
    {
        $isSame = true;
        foreach ($routeUrlSplited as $key => $item) {
            if (strpos($item, ':') !== false) {
                $currentURLSplited[$key] = $item;
            }
        }
        $isSame = count(array_diff($currentURLSplited, $routeUrlSplited)) == 0;
        return $isSame;
    }
    public function start()
    {
        $foundRoute = false;
        if (!isset($_GET['path'])) {
            return $foundRoute;
        }

        $currentURL = isset($_GET['path']) ? $_GET['path'] : explode("index.php", $_SERVER['REQUEST_URI'])[1];
        // remove first / 
        $currentURL = isset($_GET['path']) ? $currentURL :  substr($currentURL, 1);
        $urlSplited = explode('/', $currentURL);
        $sizeURL = count($urlSplited);
        foreach ($this->routes as $route) {
            $routeSize = count(explode('/', $route->url));
            $routeUrlSplited = explode('/', $route->url);
            if ($sizeURL == $routeSize && $this->hasSameParams($urlSplited, $routeUrlSplited) && $this->isSameURLString($urlSplited, $routeUrlSplited)) {
                foreach ($routeUrlSplited as $key => $item) {
                    if (strpos($item, ':') !== false) {
                        $valuePram = $urlSplited[$key];
                        $namePram = explode(':', $item)[1];

                        $this->params[$namePram] = $valuePram;
                    }
                }
                unset($_GET['path']);
                $this->params['extraParms'] = $_GET;
                $this->body = json_decode(file_get_contents('php://input'));
                $foundRoute = true;
            }
        }
        $this->routeFound = $foundRoute;
        return $foundRoute;
    }
}
