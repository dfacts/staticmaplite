<?php

namespace StaticMapLite\ElementPrinter\Polyline;

use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Point;
use StaticMapLite\Canvas\Canvas;
use StaticMapLite\Element\Polyline\Polyline;
use StaticMapLite\Util;

class PolylinePrinter
{
    /** @var int $thickness */
    protected $thickness = 3;

    /** @var Polyline $polyline */
    protected $polyline = null;

    public function __construct()
    {

    }

    public function setPolyline(Polyline $polyline): PolylinePrinter
    {
        $this->polyline = $polyline;

        return $this;
    }

    public function paint(Canvas $canvas): PolylinePrinter
    {
        $pointList = $this->convertPolylineToPointList($canvas);
        $color = $this->getPolylineColor($canvas);

        $startPoint = null;
        $endPoint = null;

        while (!empty($pointList)) {
            if (!$startPoint) {
                $startPoint = array_pop($pointList);
            }

            $endPoint = array_pop($pointList);

            $canvas->getImage()->draw()->line($startPoint, $endPoint, $color, $this->thickness);

            $startPoint = $endPoint;
        }

        return $this;
    }

    protected function getPolylineColor(Canvas $canvas): ColorInterface
    {
        return $canvas->getImage()->palette()->color([
            $this->polyline->getColorRed(),
            $this->polyline->getColorGreen(),
            $this->polyline->getColorBlue(),
        ]);
    }

    protected function convertPolylineToPointList(Canvas $canvas): array
    {
        $polylineList = \Polyline::decode($this->polyline->getPolyline());

        $pointList = [];

        while (!empty($polylineList)) {
            $latitude = array_shift($polylineList);
            $longitude = array_shift($polylineList);

            $sourceX = floor(($canvas->getWidth() / 2) - $canvas->getTileSize() * ($canvas->getCenterX() - Util::lonToTile($longitude, $canvas->getZoom())));
            $sourceY = floor(($canvas->getHeight() / 2) - $canvas->getTileSize() * ($canvas->getCenterY() - Util::latToTile($latitude, $canvas->getZoom())));

            $point = new Point($sourceX, $sourceY);

            $pointList[] = $point;
        }

        return $pointList;
    }
}
