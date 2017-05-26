<?php

namespace StaticMapLite\TileResolver;


class TileResolver
{
    public function fetch(string $url): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0');
        curl_setopt($ch, CURLOPT_URL, $url);
        $tile = curl_exec($ch);
        curl_close($ch);

        return $tile;
    }
}
