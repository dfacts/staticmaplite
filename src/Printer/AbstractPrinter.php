<?php

namespace StaticMapLite\Printer;

use StaticMapLite\Canvas\CanvasInterface;
use StaticMapLite\MapCache\MapCache;
use StaticMapLite\MapCache\MapCacheInterface;
use StaticMapLite\TileResolver\TileResolverInterface;

abstract class AbstractPrinter implements PrinterInterface
{
    /** @var MapCacheInterface $mapCache */
    protected $mapCache;

    /** @var int $maxWidth */
    protected $maxWidth = 1024;

    /** @var int $maxHeight */
    protected $maxHeight = 1024;

    /** @var TileResolverInterface */
    protected $tileResolver;

    /** @var CanvasInterface $canvas */
    protected $canvas;

    /** @var int $tileSize */
    protected $tileSize = 256;

    /** @var array $tileSrcUrl */
    protected $tileSrcUrl = [
        'mapnik' => 'http://tile.openstreetmap.org/{z}/{x}/{y}.png',
        'osmarenderer' => 'http://otile1.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png',
        'cycle' => 'http://a.tile.opencyclemap.org/cycle/{z}/{x}/{y}.png',
        'wikimedia-intl' => 'https://maps.wikimedia.org/osm-intl/{z}/{x}/{y}.png',
    ];

    /** @var string $tileDefaultSrc */
    protected $tileDefaultSrc = 'mapnik';

    /** @var bool $useTileCache */
    protected $useTileCache = true;

    /** @var int $zoom */
    protected $zoom;

    /** @var float $latitude */
    protected $latitude;

    /** @var float $longitude */
    protected $longitude;

    /** @var int $width */
    protected $width;

    /** @var int $height */
    protected $height;

    protected $image;

    protected $maptype;

    protected $centerX;

    protected $centerY;

    protected $offsetX;

    protected $offsetY;

    /** @var array $markers */
    protected $markers = [];

    /** @var array $polylines */
    protected $polylines = [];

    public function getMaxWidth(): int
    {
        return $this->maxWidth;
    }

    public function setMaxWidth(int $maxWidth): PrinterInterface
    {
        $this->maxWidth = $maxWidth;

        return $this;
    }

    public function getMaxHeight(): int
    {
        return $this->maxHeight;
    }

    public function setMaxHeight(int $maxHeight): PrinterInterface
    {
        $this->maxHeight = $maxHeight;

        return $this;
    }

    public function getTileResolver(): TileResolverInterface
    {
        return $this->tileResolver;
    }

    public function setTileResolver(TileResolverInterface $tileResolver): PrinterInterface
    {
        $this->tileResolver = $tileResolver;

        return $this;
    }

    public function getCanvas(): CanvasInterface
    {
        return $this->canvas;
    }

    public function setCanvas(CanvasInterface $canvas): PrinterInterface
    {
        $this->canvas = $canvas;

        return $this;
    }

    public function getTileSize(): int
    {
        return $this->tileSize;
    }

    public function setTileSize(int $tileSize): PrinterInterface
    {
        $this->tileSize = $tileSize;

        return $this;
    }

    public function getTileSrcUrl(): array
    {
        return $this->tileSrcUrl;
    }

    public function setTileSrcUrl(array $tileSrcUrl): PrinterInterface
    {
        $this->tileSrcUrl = $tileSrcUrl;

        return $this;
    }

    public function getTileDefaultSrc(): string
    {
        return $this->tileDefaultSrc;
    }

    public function setTileDefaultSrc(string $tileDefaultSrc): PrinterInterface
    {
        $this->tileDefaultSrc = $tileDefaultSrc;

        return $this;
    }

    public function getOsmLogo(): string
    {
        return $this->osmLogo;
    }

    public function setOsmLogo(string $osmLogo): PrinterInterface
    {
        $this->osmLogo = $osmLogo;

        return $this;
    }

    public function isUseTileCache(): bool
    {
        return $this->useTileCache;
    }

    public function setUseTileCache(bool $useTileCache): PrinterInterface
    {
        $this->useTileCache = $useTileCache;

        return $this;
    }

    public function getZoom(): int
    {
        return $this->zoom;
    }

    public function setZoom(int $zoom): PrinterInterface
    {
        $this->zoom = $zoom;

        return $this;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): PrinterInterface
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): PrinterInterface
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function setWidth($width): PrinterInterface
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function setHeight($height): PrinterInterface
    {
        $this->height = $height;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): PrinterInterface
    {
        $this->image = $image;

        return $this;
    }

    public function getMaptype(): string
    {
        return $this->maptype;
    }

    public function setMaptype(string $maptype): PrinterInterface
    {
        $this->maptype = $maptype;

        return $this;
    }

    public function getCenterX(): int
    {
        return $this->centerX;
    }

    public function setCenterX($centerX): PrinterInterface
    {
        $this->centerX = $centerX;

        return $this;
    }

    public function getCenterY(): int
    {
        return $this->centerY;
    }

    public function setCenterY(int $centerY): PrinterInterface
    {
        $this->centerY = $centerY;

        return $this;
    }

    public function getOffsetX(): int
    {
        return $this->offsetX;
    }

    public function setOffsetX(int $offsetX): PrinterInterface
    {
        $this->offsetX = $offsetX;

        return $this;
    }

    public function getOffsetY(): int
    {
        return $this->offsetY;
    }

    public function setOffsetY(int $offsetY): PrinterInterface
    {
        $this->offsetY = $offsetY;

        return $this;
    }

    public function getMarkers(): array
    {
        return $this->markers;
    }

    public function setMarkers(array $markers): PrinterInterface
    {
        $this->markers = $markers;

        return $this;
    }

    public function getPolylines(): array
    {
        return $this->polylines;
    }

    public function setPolylines(array $polylines): PrinterInterface
    {
        $this->polylines = $polylines;

        return $this;
    }
}
