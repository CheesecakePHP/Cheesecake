<?php

namespace Cheesecake\Routing;

use Cheesecake\Exception\ControllerNotExistsException;
use Cheesecake\Exception\MalformedActionException;
use Cheesecake\Exception\MethodNotExistsException;
use Cheesecake\Exception\RouteIsEmptyException;
use Cheesecake\Exception\RouteNotDefinedException;
use Cheesecake\Routing\Route;

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

    private static $routes = [
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

        if($arguments[1] === null) {
            $MatchedRoute = null;

            foreach(self::$routes[$name] as $Route) {
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

            self::$routes[$name][$route] = $Route;
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
        switch ($requestMethod) {
            case 'GET': $Route = self::get($route); break;
            case 'POST': $Route = self::post($route); break;
            case 'PUT': $Route = self::put($route); break;
            case 'PATCH': $Route = self::patch($route); break;
            case 'DELETE': $Route = self::delete($route); break;
        }

        /**
         * @TODO exec middlewares
         */

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

        return call_user_func_array([$Controller, $methodName], $Route->getData());
    }

    private static function splitAction(string $action)
    {
        if (
            strpos($action, '@') === false
            || strpos($action, '@') === 0
        ) {
            throw new MalformedActionException('Endpoint is malformed');
        }

        return ['controller' => $controller, 'method' => $method] = explode('@', $action);
    }

    /**
     * @param string $endpoint
     * @return string
     * @throws MalformedActionException
     */
    private static function getController(string $action): string
    {
        $splits = self::splitAction($action);
var_dump($splits);
        return $splits['controller'];
    }

    /**
     * @param string $endpoint
     * @return string
     * @throws MalformedActionException
     */
    private static function getMethod(string $endpoint): string
    {
        if (
            strpos($endpoint, '@') === false
            || strpos($endpoint, '@') === 0
        ) {
            throw new MalformedActionException('Action malformed');
        }

        return (explode('@', $endpoint))[1];
    }

}
