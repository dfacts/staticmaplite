<?php

namespace StaticMapLite\Canvas;

interface CanvasInterface
{
    public function getImage();
    public function getWidth(): int;
    public function getHeight(): int;
    public function getCenterX(): float;
    public function getCenterY(): float;
    public function getZoom(): int;
    public function getTileSize(): int;
}
