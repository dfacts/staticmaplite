<?php

namespace StaticMapLite\TileResolver;

use Imagine\Image\ImageInterface;

class CachedTileResolver extends TileResolver
{
    /** @var string $tileCacheBaseDir */
    protected $tileCacheBaseDir = '../cache/tiles';

    public function fetch(int $zoom, int $x, int $y): ImageInterface
    {
        $url = $this->resolve($zoom, $x, $y);

        $cachedTile = $this->checkTileCache($url);

        if ($cachedTile) {
            return $cachedTile;
        }

        $tile = parent::fetch($zoom, $x, $y);

        if ($tile) {
            $this->writeTileToCache($url, $tile);
        }

        return $tile;
    }

    protected function tileUrlToFilename(string $url): string
    {
        return $this->tileCacheBaseDir . '/' . str_replace(['http://', 'https://'], '', $url);
    }

    protected function checkTileCache(string $url): ?ImageInterface
    {
        $filename = $this->tileUrlToFilename($url);

        if (file_exists($filename)) {
            $tileImagine = $this->imagine->open($filename);

            return $tileImagine;
        }

        return null;
    }

    protected function writeTileToCache($url, ImageInterface $tileImage): CachedTileResolver
    {
        $filename = $this->tileUrlToFilename($url);

        $this->mkdir_recursive(dirname($filename), 0777);

        $tileImage->save($filename);

        return $this;
    }

    protected function mkdir_recursive($pathname, $mode): bool
    {
        is_dir(dirname($pathname)) || $this->mkdir_recursive(dirname($pathname), $mode);

        return is_dir($pathname) || @mkdir($pathname, $mode);
    }
}
