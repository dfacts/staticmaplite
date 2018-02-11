<?php

namespace StaticMapLite\TileResolver;

use Curl\Curl;

class TileResolver implements TileResolverInterface
{
    /** @var string $tileLayerUrl */
    protected $tileLayerUrl;

    /** @var Curl $curl */
    protected $curl;

    public function __construct()
    {
        $this->curl = new Curl();
    }

    public function setTileLayerUrl(string $tileLayerUrl): TileResolver
    {
        $this->tileLayerUrl = $tileLayerUrl;

        return $this;
    }

    public function resolve(int $zoom, int $x, int $y): string
    {
        return str_replace(['{z}', '{x}', '{y}'], [$zoom, $x, $y], $this->tileLayerUrl);
    }

    public function fetch(int $zoom, int $x, int $y): string
    {
        $url = $this->resolve($zoom, $x, $y);

        $this->curl->get($url);

        return $this->curl->response;
    }
}
