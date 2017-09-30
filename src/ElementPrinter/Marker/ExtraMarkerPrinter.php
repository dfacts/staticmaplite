<?php

namespace StaticMapLite\ElementPrinter\Marker;

use StaticMapLite\Canvas\Canvas;
use StaticMapLite\Element\Marker\AbstractMarker;
use StaticMapLite\Element\Marker\ExtraMarker;
use StaticMapLite\Element\Marker\Marker;
use StaticMapLite\Util;

class ExtraMarkerPrinter
{
    /** @var Marker $marker */
    protected $marker = null;

    public function __construct()
    {

    }

    public function setMarker(AbstractMarker $marker): ExtraMarkerPrinter
    {
        $this->marker = $marker;

        return $this;
    }

    public function paint(Canvas $canvas): ExtraMarkerPrinter
    {
        $extramarkersImgUrl = __DIR__.'/../../../images/extramarkers.png';
        $extramarkers = imagecreatefrompng($extramarkersImgUrl);

        $markerImage = imagecreatetruecolor(75, 100);
        $trans_colour = imagecolorallocatealpha($markerImage, 0, 0, 0, 127);
        imagefill($markerImage, 0, 0, $trans_colour);

        $destX = floor(($canvas->getWidth() / 2) - $canvas->getTileSize() * ($canvas->getCenterX() - Util::lonToTile($this->marker->getLongitude(), $canvas->getZoom())));
        $destY = floor(($canvas->getHeight() / 2) - $canvas->getTileSize() * ($canvas->getCenterY() - Util::latToTile($this->marker->getLatitude(), $canvas->getZoom())));

        $markerWidth = imagesx($markerImage);
        $markerHeight = imagesy($markerImage);

        $destX -= $markerWidth / 2;
        $destY -= $markerHeight;


        imagecopy($markerImage, $extramarkers, 0, 0, 0, 0, $markerWidth, $markerHeight);

        imagecopy($canvas->getImage(), $markerImage, $destX, $destY, 0, 0, imagesx($markerImage), imagesy($markerImage));

        return $this;
    }
}
