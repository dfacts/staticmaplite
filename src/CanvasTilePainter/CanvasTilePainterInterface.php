<?php

namespace StaticMapLite\CanvasTilePainter;

use StaticMapLite\Canvas\Canvas;
use StaticMapLite\TileResolver\TileResolver;

interface CanvasTilePainterInterface
{
    public function setCanvas(Canvas $canvas): CanvasTilePainterInterface;
    public function setTileResolver(TileResolver $tileResolver): CanvasTilePainterInterface;
    public function paint(): CanvasTilePainterInterface;
}
