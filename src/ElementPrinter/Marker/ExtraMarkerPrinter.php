<?php

namespace StaticMapLite\ElementPrinter\Marker;

use Imagine\Gd\Font;
use Imagine\Image\AbstractFont;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Point;
use Imagine\Image\Point\Center;
use StaticMapLite\Canvas\Canvas;
use StaticMapLite\Element\Marker\AbstractMarker;
use StaticMapLite\Element\Marker\ExtraMarker;
use StaticMapLite\Util;

class ExtraMarkerPrinter
{
    /** @var int $iconOffsetX */
    protected $iconOffsetX = 0;

    /** @var int $iconOffsetY */
    protected $iconOffsetY = -12;

    /** @var int $shadowOffsetX */
    protected $shadowOffsetX = 24;

    /** @var int $shadowOffsetY */
    protected $shadowOffsetY = 18;

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
        $this->paintShadow($canvas);
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

        $this->writeMarker($markerImage);

        return $markerImage;
    }

    protected function writeMarker(ImageInterface $markerImage): ExtraMarkerPrinter
    {
        $text = json_decode(sprintf('"&#x%s;"', $this->marker->getAwesome()));
        $font = $this->getFont($markerImage);

        $textBox = $font->box($text);
        $textCenterPosition = new Center($textBox);
        $imageCenterPosition = new Center($markerImage->getSize());
        $centeredTextPosition = new Point(
            $imageCenterPosition->getX() - $textCenterPosition->getX() + $this->iconOffsetX,
            $imageCenterPosition->getY() - $textCenterPosition->getY() + $this->iconOffsetY
        );

        $markerImage->draw()->text($text, $font, $centeredTextPosition);

        return $this;
    }

    protected function getFont(ImageInterface $markerImage): AbstractFont
    {
        $fontColor = $markerImage->palette()->color('fff');
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

        $point = new Point($destX + $this->shadowOffsetX, $destY + $this->shadowOffsetY);

        $canvas->getImage()->paste($shadowImage, $point);

        return $this;
    }

    protected function createShadow(): ImageInterface
    {
        $shadowImage = $this
            ->imagine
            ->open(__DIR__.'/../../../images/marker_shadow.png')
        ;

        return $shadowImage;
    }
}
