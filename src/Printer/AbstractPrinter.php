<?php

namespace StaticMapLite\Printer;

use StaticMapLite\Canvas\CanvasInterface;
use StaticMapLite\TileResolver\TileResolverInterface;

abstract class AbstractPrinter
{
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

    /** @var string $osmLogo */
    protected $osmLogo = '../images/osm_logo.png';

    /** @var bool $useTileCache */
    protected $useTileCache = true;

    protected $zoom;

    protected $lat;

    protected $lon;

    protected $width;

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

    public function setMaxWidth(int $maxWidth): AbstractPrinter
    {
        $this->maxWidth = $maxWidth;

        return $this;
    }

    public function getMaxHeight(): int
    {
        return $this->maxHeight;
    }

    public function setMaxHeight(int $maxHeight): AbstractPrinter
    {
        $this->maxHeight = $maxHeight;

        return $this;
    }

    public function getTileResolver(): TileResolverInterface
    {
        return $this->tileResolver;
    }

    public function setTileResolver(TileResolverInterface $tileResolver): AbstractPrinter
    {
        $this->tileResolver = $tileResolver;

        return $this;
    }

    public function getCanvas(): CanvasInterface
    {
        return $this->canvas;
    }

    public function setCanvas(CanvasInterface $canvas): AbstractPrinter
    {
        $this->canvas = $canvas;

        return $this;
    }

    public function getTileSize(): int
    {
        return $this->tileSize;
    }

    public function setTileSize(int $tileSize): AbstractPrinter
    {
        $this->tileSize = $tileSize;

        return $this;
    }

    public function getTileSrcUrl(): array
    {
        return $this->tileSrcUrl;
    }

    public function setTileSrcUrl(array $tileSrcUrl): AbstractPrinter
    {
        $this->tileSrcUrl = $tileSrcUrl;

        return $this;
    }

    public function getTileDefaultSrc(): string
    {
        return $this->tileDefaultSrc;
    }

    public function setTileDefaultSrc(string $tileDefaultSrc): AbstractPrinter
    {
        $this->tileDefaultSrc = $tileDefaultSrc;

        return $this;
    }

    public function getOsmLogo(): string
    {
        return $this->osmLogo;
    }

    public function setOsmLogo(string $osmLogo): AbstractPrinter
    {
        $this->osmLogo = $osmLogo;

        return $this;
    }

    public function isUseTileCache(): bool
    {
        return $this->useTileCache;
    }

    public function setUseTileCache(bool $useTileCache): AbstractPrinter
    {
        $this->useTileCache = $useTileCache;

        return $this;
    }

    public function getZoom(): int
    {
        return $this->zoom;
    }

    public function setZoom(int $zoom): AbstractPrinter
    {
        $this->zoom = $zoom;

        return $this;
    }

    public function getLat(): float
    {
        return $this->lat;
    }

    public function setLat(float $lat): AbstractPrinter
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLon(): float
    {
        return $this->lon;
    }

    public function setLon(float $lon): AbstractPrinter
    {
        $this->lon = $lon;

        return $this;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function setWidth($width): AbstractPrinter
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function setHeight($height): AbstractPrinter
    {
        $this->height = $height;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): AbstractPrinter
    {
        $this->image = $image;

        return $this;
    }

    public function getMaptype(): string
    {
        return $this->maptype;
    }

    public function setMaptype(string $maptype): AbstractPrinter
    {
        $this->maptype = $maptype;

        return $this;
    }

    public function getCenterX(): int
    {
        return $this->centerX;
    }

    public function setCenterX($centerX): AbstractPrinter
    {
        $this->centerX = $centerX;

        return $this;
    }

    public function getCenterY(): int
    {
        return $this->centerY;
    }

    public function setCenterY(int $centerY): AbstractPrinter
    {
        $this->centerY = $centerY;

        return $this;
    }

    public function getOffsetX(): int
    {
        return $this->offsetX;
    }

    public function setOffsetX(int $offsetX): AbstractPrinter
    {
        $this->offsetX = $offsetX;

        return $this;
    }

    public function getOffsetY(): int
    {
        return $this->offsetY;
    }

    public function setOffsetY(int $offsetY): AbstractPrinter
    {
        $this->offsetY = $offsetY;

        return $this;
    }

    public function getMarkers(): array
    {
        return $this->markers;
    }

    public function setMarkers(array $markers): AbstractPrinter
    {
        $this->markers = $markers;

        return $this;
    }

    public function getPolylines(): array
    {
        return $this->polylines;
    }

    public function setPolylines(array $polylines): AbstractPrinter
    {
        $this->polylines = $polylines;

        return $this;
    }
}
