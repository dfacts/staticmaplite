<?php

namespace StaticMapLite\Canvas;

use Imagine\Image\ImageInterface;

interface CanvasInterface
{
    public function getImage(): ImageInterface;
    public function getWidth(): int;
    public function getHeight(): int;
    public function getCenterX(): float;
    public function getCenterY(): float;
    public function getZoom(): int;
    public function getTileSize(): int;
}
