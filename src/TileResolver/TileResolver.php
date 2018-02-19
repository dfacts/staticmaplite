<?php

namespace StaticMapLite\TileResolver;

use Imagine\Image\ImageInterface;

class TileResolver extends AbstractTileResolver
{
    public function resolve(int $zoom, int $x, int $y): string
    {
        return str_replace(['{z}', '{x}', '{y}'], [$zoom, $x, $y], $this->tileLayerUrl);
    }

    public function fetch(int $zoom, int $x, int $y): ImageInterface
    {
        $url = $this->resolve($zoom, $x, $y);

        $this->curl->get($url);

        $image = $this
            ->imagine
            ->load($this->curl->response)
        ;

        return $image;
    }
}
