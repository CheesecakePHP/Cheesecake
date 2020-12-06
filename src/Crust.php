<?php


namespace Cheesecake;


class Crust
{

    private $Controller;
    private $method;
    private $data;
    private $error;

    /**
     * Crust constructor.
     */
    public function __construct(array $routed)
    {
        if (isset($routed['error'])) {
            $this->error = $routed['error'];
        } else {
            $this->Controller = $routed['controller'];
            $this->method = $routed['method'];
            $this->data = $routed['data'] ?? null;
        }
    }

    public function run()
    {
        if (!empty($this->error)) {
            return $this->error;
        }

        if (!is_array($this->data)) {
            $this->data = [];
        }

        return call_user_func_array([$this->Controller, $this->method], $this->data);
    }

}