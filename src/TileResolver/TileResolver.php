<?php

namespace StaticMapLite\TileResolver;

use Curl\Curl;

class TileResolver
{
    protected $tileLayer = null;

    protected $curl = null;

    public function __construct()
    {
        $this->curl = new Curl();
    }

    public function fetch(string $url): string
    {
        $this->curl->get($url);

        return $this->curl->response;
    }
}
