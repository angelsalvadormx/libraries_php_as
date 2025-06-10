<?php

include_once('response.php');
class Router
{

    private  $routes = [];
    private  $version = 'v1';
    private  $params = [];
    private  $routeFound = false;
    private  $hasAccess = null;
    private  $body;
    private  $existsRute = false;


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

            $this->hasAccess = false;
        }

        $hasAccess = $access !== false;
        if ($hasAccess) {
            $this->hasAccess = true;
        }

        return $hasAccess;
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

    public function defaultRute($callback)
    {
        $errorMsg = "";
        $errorCode = "";
        if (!$this->existsRute) {
            $errorMsg = "API not found";
            $errorCode = 404;
        }
        if ($this->existsRute && $this->hasAccess == false) {
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
        // if ($this->routeFound) {
        //     return false;
        // }

        $accces = $this->middlewares($middlewares);
        // $this->setRoute($URL, $accces);
        $existsRute = $this->existeURL($this->version . $URL);

        if ($existsRute) {
            $this->existsRute = true;
        }
        if ($existsRute && $accces) {
            $this->routeFound = true;
            return true;
        }
        return false;

        // if (!$accces || !$existsRute) {
        //     return false;
        // }

        // return true;
    }
    public function setRoute($routeName, $hasAccess)
    {
        $exist = $this->existsRoute($routeName);
        if ($exist !== false && $hasAccess) {
            throw new Error("This URL: $routeName already exists");
        }
        $splitedRoutName = explode('/', $routeName);
        array_push($this->routes, (object) array(
            "url" => $this->version . $routeName,
            "hasAccess" => $hasAccess
        ));
    }

    private function existsRoute($routeName)
    {
        $newRoute = $this->version . $routeName;
        $indexFound = array_search($newRoute, array_column($this->routes, 'url'));
        if ($indexFound !== false) {
            return $this->routes[$indexFound]->hasAccess;
        }
        return $indexFound;
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

        foreach ($routeUrlSplited as $key => $item) {
            if (strpos($item, ':') !== false) {
                $currentURLSplited[$key] = $item;
            }
        }
        $strCurrentRoute = implode("/", $currentURLSplited);
        $strRoute = implode("/", $routeUrlSplited);
        $hasSameOrder = $strCurrentRoute == $strRoute;

        $isSame = count(array_diff($currentURLSplited, $routeUrlSplited)) == 0 && $hasSameOrder;
        return $isSame;
    }

    public function existeURL($urlRequest)
    {
        $found = false;
        // if ($this->routeFound) {
        //     return false;
        // }

        $currentURL = isset($_GET['path']) ? $_GET['path'] : explode("index.php", $_SERVER['REQUEST_URI'])[1];
        // remove first / 
        $currentURL = isset($_GET['path']) ? $currentURL :  substr($currentURL, 1);
        $urlSplited = explode('/', $currentURL);
        $sizeURL = count($urlSplited);
        // foreach ($this->routes as $route) {
        $routeSize = count(explode('/', $urlRequest));
        $routeUrlSplited = explode('/', $urlRequest);
        if ($sizeURL == $routeSize && $this->hasSameParams($urlSplited, $routeUrlSplited) && $this->isSameURLString($urlSplited, $routeUrlSplited)) {
            // if ($route->hasAccess) {

            foreach ($routeUrlSplited as $key => $item) {
                if (strpos($item, ':') !== false) {
                    $valuePram = $urlSplited[$key];
                    $namePram = explode(':', $item)[1];

                    $this->params[$namePram] = $valuePram;
                }
            }

            $this->params['extraParms'] = $_GET;
            $this->body = json_decode(file_get_contents('php://input'));
            $found = true;
            // }
            // $this->existsRute = true;
        }
        // }
        return $found;
        // return $this->existsRute;
    }
}
