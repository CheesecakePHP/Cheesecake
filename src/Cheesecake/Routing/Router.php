<?php

namespace Cheesecake\Routing;

use Cheesecake\Exception\ControllerNotExistsException;
use Cheesecake\Exception\MalformedActionException;
use Cheesecake\Exception\MethodNotExistsException;
use Cheesecake\Exception\RouteIsEmptyException;
use Cheesecake\Exception\RouteNotDefinedException;

/**
 * Class Router
 * @package Cheesecake
 *
 * @method static \Cheesecake\Routing\Route|void get(string $route, string $action = null, array $options = [])
 * @method static \Cheesecake\Routing\Route|void post(string $route, string $action = null, array $options = [])
 * @method static \Cheesecake\Routing\Route|void put(string $route, string $action = null, array $options = [])
 * @method static \Cheesecake\Routing\Route|void patch(string $route, string $action = null, array $options = [])
 * @method static \Cheesecake\Routing\Route|void delete(string $route, string $action = null, array $options = [])
 */
class Router
{

    public static $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'PATCH' => [],
        'DELETE' => []
    ];

    private static $prefix;
    private static $routeOptions = [];

    public static function __callStatic(string $name, array $arguments)
    {
        $route = self::$prefix . $arguments[0];
        $route = trim($route, '/');

        if($arguments[1] === null) {
            $MatchedRoute = null;

            foreach(self::$routes[strtoupper($name)] as $Route) {
                if ($Route->match($route)) {
                    $MatchedRoute = $Route;
                    break;
                }
            }

            if($MatchedRoute === null) {
                throw new RouteNotDefinedException($route);
            }

            return $MatchedRoute;
        }
        else {
            if(empty($route)) {
                throw new RouteIsEmptyException();
            }

            $Route = new Route($route, $arguments[1]);

            if (!isset($arguments[2]) || !is_array($arguments[2])) {
                $arguments[2] = [];
            }

            $Route->setOptions(array_merge(self::$routeOptions, $arguments[2]));

            self::$routes[strtoupper($name)][$route] = $Route;
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

    public static function route(string $requestMethod, string $route)
    {
        switch($requestMethod) {
            case 'GET': $Route = self::get($route); break;
            case 'POST': $Route = self::post($route); break;
            case 'PUT': $Route = self::put($route); break;
            case 'PATCH': $Route = self::patch($route); break;
            case 'DELETE': $Route = self::delete($route); break;
        }

        $middlewares = $Route->getOptions('middleware');

        if($middlewares !== null) {
            if(!is_array($middlewares)) {
                $middlewares = [$middlewares];
            }

            foreach($middlewares as $middleware) {
                $Middleware = new $middleware();
                $result = $Middleware->handle();

                if ($result === false) {
                    return [
                        'error' => [
                            'code' => 403,
                            'message' => 'forbidden'
                        ]
                    ];
                    break;
                }
            }
        }

        $controllerName = '\App\Components\\'. self::getController($Route->getAction()) .'\Controller';
        $methodName = self::getMethod($Route->getAction());

        if (!class_exists($controllerName)) {
            throw new ControllerNotExistsException('Controller '. $controllerName .' does not exist');
        }

        $Controller = new $controllerName();

        if (!method_exists($Controller, $methodName)) {
            throw new MethodNotExistsException('Method "'. $methodName .'" does not exist in Controller '. $controllerName);
        }

        return [
            'controller' => $Controller,
            'method' => $methodName,
            'data' => $Route->getData()
        ];
    }

    /**
     * @param string $action
     * @return array
     * @throws MalformedActionException
     */
    private static function splitAction(string $action)
    {
        if (
            strpos($action, '@') === false
            || strpos($action, '@') === 0
        ) {
            throw new MalformedActionException('Action is malformed');
        }

        [$controller, $method] = explode('@', $action);

        return [
            'controller' => $controller,
            'method' => $method
        ];
    }

    /**
     * @param string $action
     * @return string
     */
    private static function getController(string $action): string
    {
        $splits = self::splitAction($action);
        return $splits['controller'];
    }

    /**
     * @param string $action
     * @return string
     */
    private static function getMethod(string $action): string
    {
        $splits = self::splitAction($action);
        return $splits['method'];
    }

}
