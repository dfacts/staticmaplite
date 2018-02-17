<?php

namespace StaticMapLite\CopyrightPrinter;

use Imagine\Gd\Imagine;
use Imagine\Image\Point;
use StaticMapLite\Canvas\CanvasInterface;

class CopyrightPrinter implements CopyrightPrinterInterface
{
    /** @var string $copyrightPath */
    protected $copyrightPath = '../images/osm_logo.png';

    /** @var CanvasInterface $canvas */
    protected $canvas;

    public function setCanvas(CanvasInterface $canvas): CopyrightPrinterInterface
    {
        $this->canvas = $canvas;

        return $this;
    }

    public function printCopyright(): CopyrightPrinterInterface
    {
        $imagine = new Imagine();
        $copyrightImage = $imagine->open($this->copyrightPath);

        $point = new Point(0,0);

        $this->canvas->getImage()->paste($copyrightImage, $point);

        return $this;
    }
}
