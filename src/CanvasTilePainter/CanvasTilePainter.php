<?php

namespace StaticMapLite\CanvasTilePainter;

use StaticMapLite\Canvas\Canvas;
use StaticMapLite\TileResolver\TileResolver;
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

    public function setTileResolver(TileResolver $tileResolver): CanvasTilePainter
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
                $tileData = $this->tileResolver->fetch($this->canvas->getZoom(), $x, $y);

                if ($tileData) {
                    $tileImage = $this->createTile($tileData);
                } else {
                    $tileImage = $this->createErrorTile();
                }

                $destX = ($x - $startX) * $this->canvas->getTileSize() + $offsetX;
                $destY = ($y - $startY) * $this->canvas->getTileSize() + $offsetY;

                imagecopy($this->canvas->getImage(), $tileImage, $destX, $destY, 0, 0, $this->canvas->getTileSize(), $this->canvas->getTileSize());
            }
        }

        return $this;
    }

    protected function createTile(string $tileData)
    {
        return imagecreatefromstring($tileData);
    }

    protected function createErrorTile()
    {
        $tileImage = imagecreate($this->canvas->getTileSize(), $this->canvas->getTileSize());
        $color = imagecolorallocate($tileImage, 255, 255, 255);
        @imagestring($tileImage, 1, 127, 127, 'err', $color);

        return $tileImage;
    }
}
