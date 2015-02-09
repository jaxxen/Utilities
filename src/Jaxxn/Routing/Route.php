<?php

namespace Jaxxn\Routing;


use Jaxxn\Utilities\GenericCollection;

class Route
{

    /**
     * @var GenericCollection
     */
    private $server;

    private $callback;

    private $parameters;

    private $currentRoute;

    function __construct(GenericCollection $server)
    {
        $this->server = $server;
        $this->server->homogenizeKeys();
    }

    public function get($path, Callable $callback)
    {
        if($this->server->get('request_method') != 'GET')
            return;

        if($this->stringContainsCurlies($path))
        {
            $this->parameters = $this->getRouteArguments($path);
            $path = $this->getFormatedPath($path);
        }

        $this->callback = $callback;
        $this->currentRoute = $this->server->get('request_uri') == $path;

        return $this;
    }


    private function getValueOfPath($value)
    {
        preg_match_all('/{(.*?)}/', $value, $matches);
        return (!empty($matches[1])) ? $matches[1][0] : false;
    }

    /**
     * @param $path
     * @return array
     */
    private function getRouteArguments($path)
    {
        $pathUrl = new UrlParser($path);
        $actualUrl = new UrlParser($this->server->get('request_uri'));

        $parameters = [];

        foreach ($pathUrl->getParts() as $key => $value) {
            if ($this->stringContainsCurlies($value) !== false) {
                $parameter = $this->getValueOfPath($value);

                if(!$parameter)
                    continue;

                $parameters[$parameter] = $actualUrl->getPart($key);
            }
        }

        return $parameters;
    }

    private function stringContainsCurlies($string)
    {
        return strpos($string, '{');
    }

    /**
     * Get formated path from arguments for comparison
     *
     * @param $path
     * @param $parameters
     * @return mixed
     */
    private function getFormatedPath($path)
    {
        foreach ($this->parameters as $key => $value) {
            $replace = sprintf("{%s}", $key);
            $path = str_replace($replace, $value, $path);
        }

        return $path;
    }
}