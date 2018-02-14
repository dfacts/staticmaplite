<?php

namespace StaticMapLite\Element\Marker;

/** @deprecated  */
class Marker extends AbstractMarker
{
    protected $markerType;

    public function __construct(string $markerType, float $latitude, float $longitude)
    {
        $this->markerType = $markerType;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function getMarkerType(): string
    {
        return $this->markerType;
    }


}
