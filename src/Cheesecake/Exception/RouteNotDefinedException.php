<?php


namespace Cheesecake\Exception;


class RouteNotDefinedException extends \Exception
{

    /**
     * RouteNotDefinedException constructor.
     */
    public function __construct(string $message)
    {
        parent::__construct('Route "'. $message .'" not defined');
    }

}