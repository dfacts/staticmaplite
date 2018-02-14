<?php

namespace StaticMapLite\Printer;

use StaticMapLite\Canvas\Canvas;
use StaticMapLite\CanvasTilePainter\CanvasTilePainter;
use StaticMapLite\CopyrightPrinter\CopyrightPrinter;
use StaticMapLite\Element\Marker\AbstractMarker;
use StaticMapLite\Element\Polyline\Polyline;
use StaticMapLite\ElementPrinter\Marker\ExtraMarkerPrinter;
use StaticMapLite\ElementPrinter\Polyline\PolylinePrinter;
use StaticMapLite\MapCache\MapCache;
use StaticMapLite\Output\CacheOutput;
use StaticMapLite\Output\ImageOutput;
use StaticMapLite\TileResolver\CachedTileResolver;
use StaticMapLite\Util;

class Printer extends AbstractPrinter
{
    public function __construct()
    {
        $this->zoom = 0;
        $this->latitude = 0;
        $this->longitude = 0;
        $this->width = 500;
        $this->height = 350;
        $this->maptype = $this->tileDefaultSrc;

        $this->mapCache = new MapCache($this);
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
        $this->latitude = $latitude;
        $this->longitude = $longitude;

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

    public function setZoom(int $zoom): PrinterInterface
    {
        $this->zoom = $zoom;

        if ($this->zoom > 18) {
            $this->zoom = 18;
        }

        return $this;
    }

    public function setMapType(string $mapType): PrinterInterface
    {
        $this->maptype = $mapType;

        $this->tileResolver->setTileLayerUrl($this->tileSrcUrl[$this->maptype]);

        return $this;
    }

    public function initCoords(): Printer
    {
        $this->centerX = Util::lonToTile($this->longitude, $this->zoom);
        $this->centerY = Util::latToTile($this->latitude, $this->zoom);

        $this->offsetX = floor((floor($this->centerX) - $this->centerX) * $this->tileSize);
        $this->offsetY = floor((floor($this->centerY) - $this->centerY) * $this->tileSize);
        
        return $this;
    }

    public function createBaseMap(): Printer
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

        return $this;
    }

    public function placeMarkers(): Printer
    {
        $printer = new ExtraMarkerPrinter();

        foreach ($this->markers as $marker) {
            $printer
                ->setMarker($marker)
                ->paint($this->canvas)
            ;
        }

        return $this;
    }

    public function placePolylines(): Printer
    {
        $printer = new PolylinePrinter();

        /** @var Polyline $polyline */
        foreach ($this->polylines as $polyline) {
            $printer
                ->setPolyline($polyline)
                ->paint($this->canvas)
            ;
        }

        return $this;
    }

    public function makeMap(): Printer
    {
        $this->initCoords();

        $this->createBaseMap();

        if (count($this->polylines)) {
            $this->placePolylines();
        }

        if (count($this->markers)) {
            $this->placeMarkers();
        }

        $this->printCopyright();

        return $this;
    }

    protected function printCopyright(): Printer
    {
        $cp = new CopyrightPrinter();
        $cp
            ->setCanvas($this->canvas)
            ->printCopyright()
        ;

        return $this;
    }

    public function showMap()
    {
        if ($this->mapCache) {
            if (!$this->mapCache->checkMapCache()) {
                $this->makeMap();

                $this->mapCache->cache($this->canvas);
            } else {
                $output = new CacheOutput();
                $output
                    ->setFilename($this->mapCache->getFilename())
                    ->sendHeader()
                    ->sendImage()
                ;
            }
        } else {
            $this->makeMap();

            $output = new ImageOutput();
            $output
                ->setImage($this->image)
                ->sendHeader()
                ->sendImage()
            ;
        }
    }
}
