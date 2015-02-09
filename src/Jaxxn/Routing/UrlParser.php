<?php

namespace Jaxxn\Routing;


class UrlParser 
{
    private $url;

    function __construct($url)
    {
        $this->url = $url;
    }

    public function getPart($index)
    {
        $parts = $this->getParts();

        return (array_key_exists($index, $parts)) ? $parts[$index] : false;
    }

    public function getParts()
    {
        return array_values(array_filter(explode('/', $this->url)));
    }
}