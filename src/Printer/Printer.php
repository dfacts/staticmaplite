<?php

namespace StaticMapLite\Printer;

use StaticMapLite\Canvas\Canvas;
use StaticMapLite\CanvasTilePainter\CanvasTilePainter;
use StaticMapLite\Element\Marker\AbstractMarker;
use StaticMapLite\Element\Polyline\Polyline;
use StaticMapLite\ElementPrinter\Marker\ExtraMarkerPrinter;
use StaticMapLite\ElementPrinter\Polyline\PolylinePrinter;
use StaticMapLite\TileResolver\CachedTileResolver;
use StaticMapLite\Util;

class Printer extends AbstractPrinter
{
    public function __construct()
    {
        $this->zoom = 0;
        $this->lat = 0;
        $this->lon = 0;
        $this->width = 500;
        $this->height = 350;
        $this->maptype = $this->tileDefaultSrc;

        $this->tileResolver = new CachedTileResolver();
        $this->tileResolver->setTileLayerUrl($this->tileSrcUrl[$this->maptype]);
    }

    public function addMarker(AbstractMarker $marker): Printer
    {
        $this->markers[] = $marker;

        return $this;
    }

    public function addPolyline(Polyline $polyline): Printer
    {
        $this->polylines[] = $polyline;

        return $this;
    }

    public function setCenter(float $latitude, float $longitude): Printer
    {
        $this->lat = $latitude;
        $this->lon = $longitude;

        return $this;
    }

    public function setSize(int $width, int $height): Printer
    {
        $this->width = $width;
        $this->height = $height;

        if ($this->width > $this->maxWidth) {
            $this->width = $this->maxWidth;
        }

        if ($this->height > $this->maxHeight) {
            $this->height = $this->maxHeight;
        }

        return $this;
    }

    public function setZoom(int $zoom): AbstractPrinter
    {
        $this->zoom = $zoom;

        if ($this->zoom > 18) {
            $this->zoom = 18;
        }

        return $this;
    }

    public function setMapType(string $mapType): AbstractPrinter
    {
        $this->maptype = $mapType;

        $this->tileResolver->setTileLayerUrl($this->tileSrcUrl[$this->maptype]);

        return $this;
    }

    public function initCoords()
    {
        $this->centerX = Util::lonToTile($this->lon, $this->zoom);
        $this->centerY = Util::latToTile($this->lat, $this->zoom);

        $this->offsetX = floor((floor($this->centerX) - $this->centerX) * $this->tileSize);
        $this->offsetY = floor((floor($this->centerY) - $this->centerY) * $this->tileSize);
    }

    public function createBaseMap()
    {
        $this->canvas = new Canvas(
            $this->width,
            $this->height,
            $this->zoom,
            $this->centerX,
            $this->centerY
        );

        $ctp = new CanvasTilePainter();
        $ctp
            ->setCanvas($this->canvas)
            ->setTileResolver($this->tileResolver)
            ->paint()
        ;
    }

    public function placeMarkers()
    {
        $printer = new ExtraMarkerPrinter();

        foreach ($this->markers as $marker) {
            $printer
                ->setMarker($marker)
                ->paint($this->canvas)
            ;
        }
    }

    public function placePolylines()
    {
        $printer = new PolylinePrinter();

        /** @var Polyline $polyline */
        foreach ($this->polylines as $polyline) {
            $printer
                ->setPolyline($polyline)
                ->paint($this->canvas)
            ;
        }
    }

    public function checkMapCache()
    {
        $this->mapCacheID = md5($this->serializeParams());
        $filename = $this->mapCacheIDToFilename();
        if (file_exists($filename)) return true;
    }

    public function serializeParams()
    {
        return join("&", array($this->zoom, $this->lat, $this->lon, $this->width, $this->height, serialize($this->markers), $this->maptype));
    }

    public function mapCacheIDToFilename()
    {
        if (!$this->mapCacheFile) {
            $this->mapCacheFile = $this->mapCacheBaseDir . "/" . $this->maptype . "/" . $this->zoom . "/cache_" . substr($this->mapCacheID, 0, 2) . "/" . substr($this->mapCacheID, 2, 2) . "/" . substr($this->mapCacheID, 4);
        }
        return $this->mapCacheFile . "." . $this->mapCacheExtension;
    }

    public function copyrightNotice()
    {
        $logoImg = imagecreatefrompng($this->osmLogo);
        imagecopy($this->canvas->getImage(), $logoImg, imagesx($this->canvas->getImage()) - imagesx($logoImg), imagesy($this->canvas->getImage()) - imagesy($logoImg), 0, 0, imagesx($logoImg), imagesy($logoImg));
    }

    public function sendHeader()
    {
        header('Content-Type: image/png');
        $expires = 60 * 60 * 24 * 14;
        header("Pragma: public");
        header("Cache-Control: maxage=" . $expires);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
    }

    public function makeMap()
    {
        $this->initCoords();
        $this->createBaseMap();

        if (count($this->polylines)) {
            $this->placePolylines();
        }

        if (count($this->markers)) {
            $this->placeMarkers();
        }

        if ($this->osmLogo) {
            $this->copyrightNotice();
        }
    }

    public function showMap()
    {
        if ($this->useMapCache) {
            // use map cache, so check cache for map
            if (!$this->checkMapCache()) {
                // map is not in cache, needs to be build
                $this->makeMap();
                $this->mkdir_recursive(dirname($this->mapCacheIDToFilename()), 0777);
                imagepng($this->canvas->getImage(), $this->mapCacheIDToFilename(), 9);
                $this->sendHeader();
                if (file_exists($this->mapCacheIDToFilename())) {
                    return file_get_contents($this->mapCacheIDToFilename());
                } else {
                    return imagepng($this->canvas->getImage());
                }
            } else {
                // map is in cache
                $this->sendHeader();
                return file_get_contents($this->mapCacheIDToFilename());
            }

        } else {
            // no cache, make map, send headers and deliver png
            $this->makeMap();
            $this->sendHeader();
            return imagepng($this->canvas->getImage());

        }
    }

    public function mkdir_recursive($pathname, $mode)
    {
        is_dir(dirname($pathname)) || $this->mkdir_recursive(dirname($pathname), $mode);
        return is_dir($pathname) || @mkdir($pathname, $mode);
    }
}
