<?php
/**
 * Created by PhpStorm.
 * User: maltehuebner
 * Date: 29.09.17
 * Time: 15:46
 */

namespace StaticMapLite\Element\Marker;


class AbstractMarker
{
    protected $latitude;
    protected $longitude;

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }
}
