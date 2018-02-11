<?php

namespace StaticMapLite\CanvasTilePainter;

interface CanvasTilePainterInterface
{
    public function setCanvas(Canvas $canvas): CanvasTilePainterInterface;
    public function setTileResolver(TileResolver $tileResolver): CanvasTilePainterInterface;
    public function paint(): CanvasTilePainterInterface;
}
