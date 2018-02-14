<?php

namespace StaticMapLite\TileResolver;

use Curl\Curl;
use Imagine\Gd\Imagine;
use Imagine\Image\ImagineInterface;

abstract class AbstractTileResolver implements TileResolverInterface
{
    /** @var string $tileLayerUrl */
    protected $tileLayerUrl;

    /** @var Curl $curl */
    protected $curl;

    /** @var ImagineInterface $imagine */
    protected $imagine;

    public function __construct()
    {
        $this->curl = new Curl();
        $this->imagine = new Imagine();
    }

    public function setTileLayerUrl(string $tileLayerUrl): TileResolverInterface
    {
        $this->tileLayerUrl = $tileLayerUrl;

        return $this;
    }
}
