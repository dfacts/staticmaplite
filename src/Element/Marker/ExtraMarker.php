<?php

namespace StaticMapLite\Element\Marker;

class ExtraMarker extends AbstractMarker
{
    const SHAPE_CIRCLE = 0;
    const SHAPE_SQUARE = 1;
    const SHAPE_STAR = 2;
    const SHAPE_PENTA = 3;

    const COLOR_RED = 0;
    const COLOR_ORANGEDARK = 1;
    const COLOR_ORANGE = 2;
    const COLOR_YELLOW = 3;
    const COLOR_BLUEDARK = 4;
    const COLOR_CYAN = 5;
    const COLOR_PURPLE = 6;
    const COLOR_VIOLET = 7;
    const COLOR_PINK = 8;
    const COLOR_GREENDARK = 9;
    const COLOR_GREEN = 10;
    const COLOR_GREENLIGHT = 11;
    const COLOR_BLACK = 12;
    const COLOR_WHITE = 13;

    protected $shape;
    protected $color;

    public function __construct(int $shape, int $color, float $latitude, float $longitude)
    {
        $this->shape = $shape;
        $this->color = $color;

        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function getShape(): int
    {
        return $this->shape;
    }

    public function getColor(): int
    {
        return $this->color;
    }
}
