<?php

namespace StaticMapLite\Element\Marker;

class Marker
{
    protected $latitude;
    protected $longitude;
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

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }
}
