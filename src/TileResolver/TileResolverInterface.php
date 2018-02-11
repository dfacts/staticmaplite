<?php

namespace StaticMapLite\TileResolver;

interface TileResolverInterface
{
    public function setTileLayerUrl(string $tileLayerUrl): TileResolver;
    public function resolve(int $zoom, int $x, int $y): string;
    public function fetch(int $zoom, int $x, int $y): string;
}
