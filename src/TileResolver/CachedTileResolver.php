<?php

namespace StaticMapLite\TileResolver;

class CachedTileResolver extends TileResolver
{
    protected $tileCacheBaseDir = '../cache/tiles';

    public function fetch(string $url): string
    {
        $cachedTile = $this->checkTileCache($url);

        if ($cachedTile) {
            return $cachedTile;
        }

        $tile = parent::fetch($url);

        if ($tile) {
            $this->writeTileToCache($url, $tile);
        }

        return $tile;
    }

    public function tileUrlToFilename(string $url): string
    {
        return $this->tileCacheBaseDir . '/' . str_replace(['http://', 'https://'], '', $url);
    }

    public function checkTileCache(string $url)
    {
        $filename = $this->tileUrlToFilename($url);

        if (file_exists($filename)) {
            return file_get_contents($filename);
        }

        return false;
    }

    public function writeTileToCache($url, $data): CachedTileResolver
    {
        $filename = $this->tileUrlToFilename($url);

        $this->mkdir_recursive(dirname($filename), 0777);

        file_put_contents($filename, $data);

        return $this;
    }

    public function mkdir_recursive($pathname, $mode): bool
    {
        is_dir(dirname($pathname)) || $this->mkdir_recursive(dirname($pathname), $mode);
        return is_dir($pathname) || @mkdir($pathname, $mode);
    }
}
