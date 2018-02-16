<?php

namespace StaticMapLite\CanvasTilePainter;

use Imagine\Image\Point;
use StaticMapLite\Canvas\Canvas;
use StaticMapLite\TileResolver\TileResolverInterface;

class CanvasTilePainter
{
    /** @var Canvas $canvas */
    protected $canvas;

    /** @var TileResolverInterface $tileResolver */
    protected $tileResolver;

    public function __construct()
    {

    }

    public function setCanvas(Canvas $canvas): CanvasTilePainter
    {
        $this->canvas = $canvas;

        return $this;
    }

    public function setTileResolver(TileResolverInterface $tileResolver): CanvasTilePainter
    {
        $this->tileResolver = $tileResolver;

        return $this;
    }

    public function paint(): CanvasTilePainter
    {
        $startX = floor($this->canvas->getCenterX() - ($this->canvas->getWidth() / $this->canvas->getTileSize()) / 2);
        $startY = floor($this->canvas->getCenterY() - ($this->canvas->getHeight() / $this->canvas->getTileSize()) / 2);
        $endX = ceil($this->canvas->getCenterX() + ($this->canvas->getWidth() / $this->canvas->getTileSize()) / 2);
        $endY = ceil($this->canvas->getCenterY() + ($this->canvas->getHeight() / $this->canvas->getTileSize()) / 2);

        $offsetX = -floor(($this->canvas->getCenterX() - floor($this->canvas->getCenterX())) * $this->canvas->getTileSize());
        $offsetY = -floor(($this->canvas->getCenterY() - floor($this->canvas->getCenterY())) * $this->canvas->getTileSize());
        $offsetX += floor($this->canvas->getWidth() / 2);
        $offsetY += floor($this->canvas->getHeight() / 2);
        $offsetX += floor($startX - floor($this->canvas->getCenterX())) * $this->canvas->getTileSize();
        $offsetY += floor($startY - floor($this->canvas->getCenterY())) * $this->canvas->getTileSize();

        for ($x = $startX; $x <= $endX; $x++) {
            for ($y = $startY; $y <= $endY; $y++) {
                $tileImage = $this->tileResolver->fetch($this->canvas->getZoom(), $x, $y);

                $destX = ($x - $startX) * $this->canvas->getTileSize() + $offsetX;
                $destY = ($y - $startY) * $this->canvas->getTileSize() + $offsetY;

                if ($destX >= 0 && $destY >= 0 && $destX < $this->canvas->getWidth() && $destY < $this->canvas->getHeight()) {
                    $point = new Point($destX, $destY);
                    $tileImage->paste($this->canvas->getImage(), $point);
                }
            }
        }

        return $this;
    }
}
