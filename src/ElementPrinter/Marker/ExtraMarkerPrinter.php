<?php

namespace StaticMapLite\ElementPrinter\Marker;

use Imagine\Gd\Font;
use Imagine\Image\Box;
use Imagine\Image\FontInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Point;
use StaticMapLite\Canvas\Canvas;
use StaticMapLite\Element\Marker\AbstractMarker;
use StaticMapLite\Element\Marker\ExtraMarker;
use StaticMapLite\Util;

class ExtraMarkerPrinter
{
    /** @var ImagineInterface $imagine */
    protected $imagine;

    /** @var ExtraMarker $marker */
    protected $marker = null;

    /** @var int $baseMarkerWidth */
    protected $baseMarkerWidth = 72;

    /** @var int $baseMarkerHeight */
    protected $baseMarkerHeight = 92;

    /** @var int $baseShadowWidth */
    protected $baseShadowWidth = 21;

    /** @var int $baseShadowHeight */
    protected $baseShadowHeight = 14;

    /** @var float $markerSize */
    protected $markerSize = 0.75;

    public function __construct()
    {
        $this->imagine = new \Imagine\Gd\Imagine();
    }

    public function setMarkerSize(float $markerSize): ExtraMarkerPrinter
    {
        $this->markerSize = $markerSize;

        return $this;
    }

    public function setMarker(AbstractMarker $marker): ExtraMarkerPrinter
    {
        $this->marker = $marker;

        return $this;
    }

    public function paint(Canvas $canvas): ExtraMarkerPrinter
    {
        //$this->paintShadow($canvas); looks like shadows are currently broken, will fix that later
        $this->paintMarker($canvas);

        return $this;
    }

    protected function paintMarker(Canvas $canvas): ExtraMarkerPrinter
    {
        $markerImage = $this->createMarker();

        $destX = floor(($canvas->getWidth() / 2) - $canvas->getTileSize() * ($canvas->getCenterX() - Util::lonToTile($this->marker->getLongitude(), $canvas->getZoom())));
        $destY = floor(($canvas->getHeight() / 2) - $canvas->getTileSize() * ($canvas->getCenterY() - Util::latToTile($this->marker->getLatitude(), $canvas->getZoom())));

        $destX -= $markerImage->getSize()->getWidth() * $this->markerSize / 2;
        $destY -= $markerImage->getSize()->getHeight() * $this->markerSize;

        $point = new Point($destX, $destY);

        if ($canvas->getImage()->getSize()->contains($markerImage->getSize(), $point)) {
            $canvas->getImage()->paste($markerImage, $point);
        }

        return $this;
    }

    protected function createMarker(): ImageInterface
    {
        $extramarkers = $this
            ->imagine
            ->open(__DIR__.'/../../../images/extramarkers.png')
        ;

        $sourceX = $this->baseMarkerWidth * $this->marker->getColor();
        $sourceY = $this->baseMarkerHeight * $this->marker->getShape();

        $point = new Point($sourceX, $sourceY);
        $box = new Box($this->baseMarkerWidth, $this->baseMarkerHeight);

        $markerImage = $extramarkers->crop($point, $box);

        //$this->writeMarker($markerImage);

        return $markerImage;
    }

    protected function writeMarker(ImageInterface $markerImage): ExtraMarkerPrinter
    {
        $text = json_decode(sprintf('"&#x%s;"', $this->marker->getAwesome()));

        $bbox = imagettfbbox($fontSize, 0, $fontFile, $text);

        $x = $bbox[0] + (imagesx($markerImage) / 2) - ($bbox[4] / 2) + 3;
        $y = 42;


        imagettftext($markerImage, $fontSize, 0, $x, $y, $white, $fontFile, $text);

        return $this;
    }

    protected function getFont(ImageInterface $markerImage): FontInterface
    {
        $fontColor = $markerImage->palette()->color('white');
        $fontSize = 20;
        $fontFilename = __DIR__.'/../../../fonts/fontawesome-webfont.ttf';

        $font = new Font($fontFilename, $fontSize, $fontColor);

        return $font;
    }

    protected function paintShadow(Canvas $canvas): ExtraMarkerPrinter
    {
        $shadowImage = $this->createShadow();

        $destX = floor(($canvas->getWidth() / 2) - $canvas->getTileSize() * ($canvas->getCenterX() - Util::lonToTile($this->marker->getLongitude(), $canvas->getZoom())));
        $destY = floor(($canvas->getHeight() / 2) - $canvas->getTileSize() * ($canvas->getCenterY() - Util::latToTile($this->marker->getLatitude(), $canvas->getZoom())));

        $destX -= $this->baseShadowWidth * $this->markerSize;
        $destY -= $this->baseShadowHeight;

        imagecopyresampled(
            $canvas->getImage(),
            $shadowImage,
            $destX,
            $destY,
            0,
            0,
            $this->baseShadowWidth,
            $this->baseShadowHeight,
            $this->baseShadowWidth,
            $this->baseShadowHeight
        );

        return $this;
    }

    protected function createShadow()
    {
        $shadowImgUrl = __DIR__.'/../../../images/marker_shadow.png';
        $shadow = imagecreatefrompng($shadowImgUrl);

        $shadowImage = imagecreatetruecolor(21, 14);
        $transparentColor = imagecolorallocatealpha($shadowImage, 255, 255, 255, 0);
        imagefill($shadowImage, 0, 0, $transparentColor);

        imagecopy(
            $shadow,
            $shadowImage,
            0,
            0,
            0,
            0,
            $this->baseShadowWidth,
            $this->baseShadowHeight
        );

        return $shadowImage;
    }
}
