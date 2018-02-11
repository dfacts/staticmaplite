<?php

namespace StaticMapLite\Canvas;

class Canvas implements CanvasInterface
{
    protected $image = null;

    /** @var int $tileSize */
    protected $tileSize = 256;

    /** @var int $width */
    protected $width = 0;

    /** @var int $height */
    protected $height = 0;

    /** @var float $centerX */
    protected $centerX = 0;

    /** @var float $centerY */
    protected $centerY = 0;

    /** @var int $zoom */
    protected $zoom = 0;

    public function __construct(int $width, int $height, int $zoom, float $centerX, float $centerY)
    {
        $this->width = $width;
        $this->height = $height;

        $this->zoom = $zoom;

        $this->centerX = $centerX;
        $this->centerY = $centerY;

        $this->image = imagecreatetruecolor($this->width, $this->height);
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getCenterX(): float
    {
        return $this->centerX;
    }

    public function getCenterY(): float
    {
        return $this->centerY;
    }

    public function getZoom(): int
    {
        return $this->zoom;
    }

    public function getTileSize(): int
    {
        return $this->tileSize;
    }
}
