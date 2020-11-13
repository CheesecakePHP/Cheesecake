<?php


namespace Cheesecake\Routing;


Interface IRoute
{

    public function __construct(string $route, string $endpoint);
    public function setData(array $data);
    public function setOptions(array $options);
    public function get();
    public function match(string $route);

}