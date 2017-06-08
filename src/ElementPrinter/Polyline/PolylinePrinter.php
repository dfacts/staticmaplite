<?php

namespace StaticMapLite\ElementPrinter\Polyline;

use StaticMapLite\Canvas\Canvas;
use StaticMapLite\Element\Polyline\Polyline;
use StaticMapLite\Util;

class PolylinePrinter
{
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
        $polylineList = \Polyline::decode($this->polyline->getPolyline());

        $sourceLatitude = null;
        $sourceLongitude = null;
        $destinationLatitude = null;
        $destinationLongitude = null;

        $color = imagecolorallocate($canvas->getImage(), $this->polyline->getColorRed(), $this->polyline->getColorGreen(), $this->polyline->getColorBlue());
        imagesetthickness($canvas->getImage(), 5);
        //imageantialias($this->image, true);

        while (!empty($polylineList)) {
            if (!$sourceLatitude) {
                $sourceLatitude = array_shift($polylineList);
            }

            if (!$sourceLongitude) {
                $sourceLongitude = array_shift($polylineList);
            }

            $sourceX = floor(($canvas->getWidth() / 2) - $canvas->getTileSize() * ($canvas->getCenterX() - Util::lonToTile($sourceLongitude, $canvas->getZoom())));
            $sourceY = floor(($canvas->getHeight() / 2) - $canvas->getTileSize() * ($canvas->getCenterY() - Util::latToTile($sourceLatitude, $canvas->getZoom())));

            $destinationLatitude = array_shift($polylineList);
            $destinationLongitude = array_shift($polylineList);

            $destinationX = floor(($canvas->getWidth() / 2) - $canvas->getTileSize() * ($canvas->getCenterX() - Util::lonToTile($destinationLongitude, $canvas->getZoom())));
            $destinationY = floor(($canvas->getHeight() / 2) - $canvas->getTileSize() * ($canvas->getCenterY() - Util::latToTile($destinationLatitude, $canvas->getZoom())));

            imageline($canvas->getImage() , $sourceX, $sourceY , $destinationX, $destinationY, $color);

            $sourceLatitude = $destinationLatitude;
            $sourceLongitude = $destinationLongitude;
        }

        return $this;
    }
}