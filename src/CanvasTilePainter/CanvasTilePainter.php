<?php

namespace StaticMapLite\CanvasTilePainter;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use StaticMapLite\Canvas\Canvas;
use StaticMapLite\TileResolver\TileResolverInterface;

class CanvasTilePainter
{
    /** @var Canvas $canvas */
    protected $canvas;

    /** @var TileResolverInterface $tileResolver */
    protected $tileResolver;

    /** @var ImageInterface $tmpCanvasImage */
    protected $tmpCanvasImage;

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

    protected function createTmpCanvasImage(): CanvasTilePainter
    {
        $tileSize = $this->canvas->getTileSize();

        $size = new Box($this->canvas->getWidth() + 2 * $tileSize, $this->canvas->getHeight() + 2 * $tileSize);

        $imagine = new Imagine();
        $this->tmpCanvasImage = $imagine->create($size);

        return $this;
    }

    protected function cropToCanvas(): CanvasTilePainter
    {
        $tmpTopLeftPoint = new Point(256, 256);
        $this->tmpCanvasImage->crop($tmpTopLeftPoint, $this->canvas->getImage()->getSize());

        $topLeftPoint = new Point(0, 0);
        $this->canvas->getImage()->paste($this->tmpCanvasImage, $topLeftPoint);

        return $this;
    }

    public function paint(): CanvasTilePainter
    {
        $this->createTmpCanvasImage();

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

        $box = new Box(256, 256);

        for ($x = $startX; $x <= $endX; $x++) {
            for ($y = $startY; $y <= $endY; $y++) {
                $tileImage = $this->tileResolver->fetch($this->canvas->getZoom(), $x, $y);

                $destX = ($x - $startX) * $this->canvas->getTileSize() + $offsetX;
                $destY = ($y - $startY) * $this->canvas->getTileSize() + $offsetY;

                $point = new Point($destX + 256, $destY + 256);

                if ($this->tmpCanvasImage->getSize()->contains($box, $point)) {
                    $this->tmpCanvasImage->paste($tileImage, $point);
                }
            }
        }

        $this->cropToCanvas();

        return $this;
    }
}
