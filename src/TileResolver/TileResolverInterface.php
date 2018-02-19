<?php

namespace StaticMapLite\TileResolver;

use Imagine\Image\ImageInterface;

interface TileResolverInterface
{
    public function setTileLayerUrl(string $tileLayerUrl): TileResolverInterface;
    public function resolve(int $zoom, int $x, int $y): string;
    public function fetch(int $zoom, int $x, int $y): ImageInterface;
}
