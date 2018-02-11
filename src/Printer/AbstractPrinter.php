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

    /** @var bool $useMapCache */
    protected $useMapCache = false;

    /** @var string $mapCacheBaseDir */
    protected $mapCacheBaseDir = '../cache/maps';

    /** @var string $mapCacheID */
    protected $mapCacheID = '';

    /** @var string $mapCacheFile */
    protected $mapCacheFile = '';

    /** @var string $mapCacheExtension */
    protected $mapCacheExtension = 'png';

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

    /**
     * @return int
     */
    public function getMaxWidth(): int
    {
        return $this->maxWidth;
    }

    /**
     * @param int $maxWidth
     * @return AbstractPrinter
     */
    public function setMaxWidth(int $maxWidth): AbstractPrinter
    {
        $this->maxWidth = $maxWidth;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxHeight(): int
    {
        return $this->maxHeight;
    }

    /**
     * @param int $maxHeight
     * @return AbstractPrinter
     */
    public function setMaxHeight(int $maxHeight): AbstractPrinter
    {
        $this->maxHeight = $maxHeight;
        return $this;
    }

    /**
     * @return TileResolverInterface
     */
    public function getTileResolver(): TileResolverInterface
    {
        return $this->tileResolver;
    }

    /**
     * @param TileResolverInterface $tileResolver
     * @return AbstractPrinter
     */
    public function setTileResolver(TileResolverInterface $tileResolver): AbstractPrinter
    {
        $this->tileResolver = $tileResolver;
        return $this;
    }

    /**
     * @return CanvasInterface
     */
    public function getCanvas(): CanvasInterface
    {
        return $this->canvas;
    }

    /**
     * @param CanvasInterface $canvas
     * @return AbstractPrinter
     */
    public function setCanvas(CanvasInterface $canvas): AbstractPrinter
    {
        $this->canvas = $canvas;
        return $this;
    }

    /**
     * @return int
     */
    public function getTileSize(): int
    {
        return $this->tileSize;
    }

    /**
     * @param int $tileSize
     * @return AbstractPrinter
     */
    public function setTileSize(int $tileSize): AbstractPrinter
    {
        $this->tileSize = $tileSize;
        return $this;
    }

    /**
     * @return array
     */
    public function getTileSrcUrl(): array
    {
        return $this->tileSrcUrl;
    }

    /**
     * @param array $tileSrcUrl
     * @return AbstractPrinter
     */
    public function setTileSrcUrl(array $tileSrcUrl): AbstractPrinter
    {
        $this->tileSrcUrl = $tileSrcUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getTileDefaultSrc(): string
    {
        return $this->tileDefaultSrc;
    }

    /**
     * @param string $tileDefaultSrc
     * @return AbstractPrinter
     */
    public function setTileDefaultSrc(string $tileDefaultSrc): AbstractPrinter
    {
        $this->tileDefaultSrc = $tileDefaultSrc;
        return $this;
    }

    /**
     * @return string
     */
    public function getOsmLogo(): string
    {
        return $this->osmLogo;
    }

    /**
     * @param string $osmLogo
     * @return AbstractPrinter
     */
    public function setOsmLogo(string $osmLogo): AbstractPrinter
    {
        $this->osmLogo = $osmLogo;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUseTileCache(): bool
    {
        return $this->useTileCache;
    }

    /**
     * @param bool $useTileCache
     * @return AbstractPrinter
     */
    public function setUseTileCache(bool $useTileCache): AbstractPrinter
    {
        $this->useTileCache = $useTileCache;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUseMapCache(): bool
    {
        return $this->useMapCache;
    }

    /**
     * @param bool $useMapCache
     * @return AbstractPrinter
     */
    public function setUseMapCache(bool $useMapCache): AbstractPrinter
    {
        $this->useMapCache = $useMapCache;
        return $this;
    }

    /**
     * @return string
     */
    public function getMapCacheBaseDir(): string
    {
        return $this->mapCacheBaseDir;
    }

    /**
     * @param string $mapCacheBaseDir
     * @return AbstractPrinter
     */
    public function setMapCacheBaseDir(string $mapCacheBaseDir): AbstractPrinter
    {
        $this->mapCacheBaseDir = $mapCacheBaseDir;
        return $this;
    }

    /**
     * @return string
     */
    public function getMapCacheID(): string
    {
        return $this->mapCacheID;
    }

    /**
     * @param string $mapCacheID
     * @return AbstractPrinter
     */
    public function setMapCacheID(string $mapCacheID): AbstractPrinter
    {
        $this->mapCacheID = $mapCacheID;
        return $this;
    }

    /**
     * @return string
     */
    public function getMapCacheFile(): string
    {
        return $this->mapCacheFile;
    }

    /**
     * @param string $mapCacheFile
     * @return AbstractPrinter
     */
    public function setMapCacheFile(string $mapCacheFile): AbstractPrinter
    {
        $this->mapCacheFile = $mapCacheFile;
        return $this;
    }

    /**
     * @return string
     */
    public function getMapCacheExtension(): string
    {
        return $this->mapCacheExtension;
    }

    /**
     * @param string $mapCacheExtension
     * @return AbstractPrinter
     */
    public function setMapCacheExtension(string $mapCacheExtension): AbstractPrinter
    {
        $this->mapCacheExtension = $mapCacheExtension;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getZoom()
    {
        return $this->zoom;
    }

    /**
     * @param mixed $zoom
     * @return AbstractPrinter
     */
    public function setZoom($zoom)
    {
        $this->zoom = $zoom;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @param mixed $lat
     * @return AbstractPrinter
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLon()
    {
        return $this->lon;
    }

    /**
     * @param mixed $lon
     * @return AbstractPrinter
     */
    public function setLon($lon)
    {
        $this->lon = $lon;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param mixed $width
     * @return AbstractPrinter
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param mixed $height
     * @return AbstractPrinter
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     * @return AbstractPrinter
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaptype()
    {
        return $this->maptype;
    }

    /**
     * @param mixed $maptype
     * @return AbstractPrinter
     */
    public function setMaptype($maptype)
    {
        $this->maptype = $maptype;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCenterX()
    {
        return $this->centerX;
    }

    /**
     * @param mixed $centerX
     * @return AbstractPrinter
     */
    public function setCenterX($centerX)
    {
        $this->centerX = $centerX;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCenterY()
    {
        return $this->centerY;
    }

    /**
     * @param mixed $centerY
     * @return AbstractPrinter
     */
    public function setCenterY($centerY)
    {
        $this->centerY = $centerY;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOffsetX()
    {
        return $this->offsetX;
    }

    /**
     * @param mixed $offsetX
     * @return AbstractPrinter
     */
    public function setOffsetX($offsetX)
    {
        $this->offsetX = $offsetX;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOffsetY()
    {
        return $this->offsetY;
    }

    /**
     * @param mixed $offsetY
     * @return AbstractPrinter
     */
    public function setOffsetY($offsetY)
    {
        $this->offsetY = $offsetY;
        return $this;
    }

    /**
     * @return array
     */
    public function getMarkers(): array
    {
        return $this->markers;
    }

    /**
     * @param array $markers
     * @return AbstractPrinter
     */
    public function setMarkers(array $markers): AbstractPrinter
    {
        $this->markers = $markers;
        return $this;
    }

    /**
     * @return array
     */
    public function getPolylines(): array
    {
        return $this->polylines;
    }

    /**
     * @param array $polylines
     * @return AbstractPrinter
     */
    public function setPolylines(array $polylines): AbstractPrinter
    {
        $this->polylines = $polylines;
        return $this;
    }
}
