<?php

namespace StaticMapLite\Element\Polyline;

class Polyline
{
    /** @var string $polyline */
    protected $polyline = null;

    /** @var int $colorRed */
    protected $colorRed = 0;

    /** @var int $colorGreen */
    protected $colorGreen = 0;

    /** @var int $colorBlue */
    protected $colorBlue = 0;

    public function __construct(string $polyline, int $colorRed = 0, int $colorGreen = 0, int $colorBlue = 0)
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
