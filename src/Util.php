<?php

namespace StaticMapLite;

class Util
{
    public static function lonToTile(float $longitude, int $zoom): float
    {
        return (($longitude + 180) / 360) * pow(2, $zoom);
    }

    public static function latToTile(float $latitude, int $zoom): float
    {
        return (1 - log(tan($latitude * pi() / 180) + 1 / cos($latitude * pi() / 180)) / pi()) / 2 * pow(2, $zoom);
    }
}
