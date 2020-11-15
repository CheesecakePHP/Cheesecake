<?php


namespace Cheesecake\Routing;


class Route implements IRoute
{

    private string $route;
    private string $action;
    private array $data = [];
    private array $options = [];

    public function __construct(string $route, string $action)
    {
        $this->route = $route;
        $this->action = $action;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $key
     * @return array|mixed
     */
    public function getOptions(string $key = null)
    {
        $return = $this->options;

        if ($key !== null) {
            if (!isset($this->options[$key])) {
                $this->options[$key] = null;
            }

            $return = $this->options[$key];
        }

        return $return;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    public function get()
    {
        return $this;
    }

    public function match(string $uri)
    {
        $uri = trim($uri, '/');
        $route = preg_replace('~\{(\w+)\}~', '(\w+)', $this->route);

        $matched = (bool)preg_match_all('~^'. $route .'$~', $uri, $matches, PREG_SET_ORDER);

        if ($matched) {
            preg_match_all('~\{(\w+)\}~', $this->route, $placeholders);

            $data = [];

            foreach ($placeholders[1] as $k => $placeholder) {
                $data[$placeholder] = $matches[0][($k+1)];
            }

            $this->setData($data);
        }

        return $matched;
    }
}