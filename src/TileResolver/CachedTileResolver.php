<?php

namespace StaticMapLite\TileResolver;

class CachedTileResolver extends TileResolver
{
    public function fetch(string $url): string
    {
/*
        if ($this->useTileCache && ($cached = $this->checkTileCache($url))) return $cached;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0");
        curl_setopt($ch, CURLOPT_URL, $url);
        $tile = curl_exec($ch);
        curl_close($ch);
        if ($tile && $this->useTileCache) {
            $this->writeTileToCache($url, $tile);
        }
        return $tile;
*/

        throw new \Exception('Not implemented');
    }
}
