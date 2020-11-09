<?php

namespace Cheesecake;

use Cheesecake\Exception\RouteIsEmptyException;
use Cheesecake\Exception\RouteNotDefinedException;

class Router
{

    private static $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'PATCH' => [],
        'DELETE' => []
    ];

    private static $prefix;
    private static $routeOptions = [];

    public static function get(string $route, ?string $endpoint = null, array $data = [], array $options = [])
    {
        if($endpoint === null) {
            if(!isset(self::$routes['GET'][$route])) {
                throw new RouteNotDefinedException($route);
            }

            return self::$routes['GET'][$route] ?? null;
        }
        else {
            if(empty($route)) {
                throw new RouteIsEmptyException();
            }

            self::$routes['GET'][self::$prefix . $route] = [
                'type' => 'GET',
                'endpoint' => $endpoint,
                'data' => $data,
                'options' => array_merge(self::$routeOptions, $options)
            ];
        }
    }

    public static function post(string $route, ?string $endpoint = null, array $data = [], array $options = [])
    {
        if($endpoint === null) {
            if(!isset(self::$routes['GET'][$route])) {
                throw new RouteNotDefinedException($route);
            }

            return self::$routes['POST'][$route] ?? null;
        }
        else {
            if(empty($route)) {
                throw new RouteIsEmptyException();
            }

            self::$routes['POST'][self::$prefix . $route] = [
                'type' => 'POST',
                'endpoint' => $endpoint,
                'data' => $data,
                'options' => array_merge(self::$routeOptions, $options)
            ];
        }
    }

    public static function put(string $route, ?string $endpoint = null, array $data = [], array $options = [])
    {
        if($endpoint === null) {
            if(!isset(self::$routes['GET'][$route])) {
                throw new RouteNotDefinedException($route);
            }

            return self::$routes['PUT'][$route] ?? null;
        }
        else {
            if(empty($route)) {
                throw new RouteIsEmptyException();
            }

            self::$routes['PUT'][self::$prefix . $route] = [
                'type' => 'PUT',
                'endpoint' => $endpoint,
                'data' => $data,
                'options' => array_merge(self::$routeOptions, $options)
            ];
        }
    }

    public static function patch(string $route, ?string $endpoint = null, array $data = [], array $options = [])
    {
        if($endpoint === null) {
            if(!isset(self::$routes['GET'][$route])) {
                throw new RouteNotDefinedException($route);
            }

            return self::$routes['PATCH'][$route] ?? null;
        }
        else {
            if(empty($route)) {
                throw new RouteIsEmptyException();
            }

            self::$routes['PATCH'][self::$prefix . $route] = [
                'type' => 'PATCH',
                'endpoint' => $endpoint,
                'data' => $data,
                'options' => array_merge(self::$routeOptions, $options)
            ];
        }
    }

    public static function delete(string $route, ?string $endpoint = null, array $data = [], array $options = [])
    {
        if($endpoint === null) {
            if(!isset(self::$routes['GET'][$route])) {
                throw new RouteNotDefinedException($route);
            }

            return self::$routes['DELETE'][$route] ?? null;
        }
        else {
            if(empty($route)) {
                throw new RouteIsEmptyException();
            }

            self::$routes['DELETE'][self::$prefix . $route] = [
                'type' => 'DELETE',
                'endpoint' => $endpoint,
                'data' => $data,
                'options' => array_merge(self::$routeOptions, $options)
            ];
        }
    }

    public static function group(array $sharedOptions, callable $callback)
    {
        if(isset($sharedOptions['prefix'])) {
            self::$prefix = $sharedOptions['prefix'];
        }

        if(isset($sharedOptions['middleware'])) {
            self::$routeOptions['middleware'] = $sharedOptions['middleware'];
        }

        $callback();

        self::$prefix = null;
        self::$routeOptions = [];
    }

    public static function match(string $type, string $route): bool
    {
        $return = false;

        if (isset(self::$routes[$type][$route])) {
            $return = true;
        }

        return $return;
    }

    public static function route(string $requestMethod, string $route)
    {
        switch ($requestMethod) {
            case 'GET': $route = self::get($route); break;
            case 'POST': $route = self::post($route); break;
            case 'PUT': $route = self::put($route); break;
            case 'PATCH': $route = self::patch($route); break;
            case 'DELETE': $route = self::delete($route); break;
        }

        /**
         * @TODO exec middlewares
         */

        $controllerName = '\App\Components\\'. self::getController($route['endpoint']) .'\Controller';
        $methodName = self::getMethod($route['endpoint']);



        if (!class_exists($controllerName)) {
            throw new \Cheesecake\Exception\ControllerNotExistsException('Controller '. $controllerName .' does not exist');
        }

        $Controller = new $controllerName();

        if (!method_exists($Controller, $methodName)) {
            throw new \Cheesecake\Exception\MethodNotExistsException('Method "'. $methodName .'" does not exist in Controller '. $controllerName);
        }

        return [
            'controller' => $Controller,
            'method' => $methodName,
            'data' => $route['data']
        ];

        return call_user_func_array([$Controller, $methodName], $route['data']);
    }

    private static function getController(string $endpoint): string
    {
        if (
            strpos($endpoint, '@') === false
            || strpos($endpoint, '@') === 0
        ) {
            throw new \Cheesecake\Exception\MalformedEndpointException('Endpoint is malformed');
        }

        return (explode('@', $endpoint))[0];
    }

    private static function getMethod(string $endpoint): string
    {
        if (
            strpos($endpoint, '@') === false
            || strpos($endpoint, '@') === 0
        ) {
            throw new \Exception('Endpoint malformed');
        }

        return (explode('@', $endpoint))[1];
    }

}
