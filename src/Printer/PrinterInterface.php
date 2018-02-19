<?php

namespace StaticMapLite\Printer;

use StaticMapLite\Canvas\CanvasInterface;
use StaticMapLite\TileResolver\TileResolverInterface;

interface PrinterInterface
{
    public function getMaxWidth(): int;
    public function setMaxWidth(int $maxWidth): PrinterInterface;

    public function getMaxHeight(): int;
    public function setMaxHeight(int $maxHeight): PrinterInterface;

    public function getTileResolver(): TileResolverInterface;
    public function setTileResolver(TileResolverInterface $tileResolver): PrinterInterface;

    public function getCanvas(): CanvasInterface;
    public function setCanvas(CanvasInterface $canvas): PrinterInterface;

    public function getTileSize(): int;
    public function setTileSize(int $tileSize): PrinterInterface;

    public function getTileSrcUrl(): array;
    public function setTileSrcUrl(array $tileSrcUrl): PrinterInterface;

    public function getTileDefaultSrc(): string;
    public function setTileDefaultSrc(string $tileDefaultSrc): PrinterInterface;

    public function getOsmLogo(): string;
    public function setOsmLogo(string $osmLogo): PrinterInterface;

    public function isUseTileCache(): bool;
    public function setUseTileCache(bool $useTileCache): PrinterInterface;

    public function getZoom(): int;
    public function setZoom(int $zoom): PrinterInterface;

    public function getLatitude(): float;
    public function setLatitude(float $lat): PrinterInterface;

    public function getLongitude(): float;
    public function setLongitude(float $lon): PrinterInterface;

    public function getWidth(): int;
    public function setWidth($width): PrinterInterface;

    public function getHeight(): int;
    public function setHeight($height): PrinterInterface;

    public function getImage();
    public function setImage($image): PrinterInterface;

    public function getMaptype(): string;
    public function setMaptype(string $maptype): PrinterInterface;

    public function getCenterX(): int;
    public function setCenterX($centerX): PrinterInterface;

    public function getCenterY(): int;
    public function setCenterY(int $centerY): PrinterInterface;

    public function getOffsetX(): int;
    public function setOffsetX(int $offsetX): PrinterInterface;

    public function getOffsetY(): int;
    public function setOffsetY(int $offsetY): PrinterInterface;

    public function getMarkers(): array;
    public function setMarkers(array $markers): PrinterInterface;

    public function getPolylines(): array;
    public function setPolylines(array $polylines): PrinterInterface;
}
