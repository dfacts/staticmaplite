<?php

namespace StaticMapLite\Element\Polyline;

class Polyline
{
    protected $polyline = null;
    protected $colorRed = 0;
    protected $colorGreen = 0;
    protected $colorBlue = 0;

    public function __construct(string $polyline, int $colorRed, int $colorGreen, int $colorBlue)
    {
        $this->polyline = $polyline;

        $this->colorRed = $colorRed;
        $this->colorGreen = $colorGreen;
        $this->colorBlue = $colorBlue;
    }

    public function getPolyline(): string
    {
        return $this->polyline;
    }

    public function getColorRed(): int
    {
        return $this->colorRed;
    }

    public function getColorGreen(): int
    {
        return $this->colorGreen;
    }

    public function getColorBlue(): int
    {
        return $this->colorBlue;
    }
}
