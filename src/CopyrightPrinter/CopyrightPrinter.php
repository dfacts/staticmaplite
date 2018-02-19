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

    /** @var int $offset */
    protected $offset = 10;

    public function setCanvas(CanvasInterface $canvas): CopyrightPrinterInterface
    {
        $this->canvas = $canvas;

        return $this;
    }

    public function printCopyright(): CopyrightPrinterInterface
    {
        $imagine = new Imagine();
        $copyrightImage = $imagine->open($this->copyrightPath);

        $canvasSize = $this->canvas->getImage()->getSize();
        $copyrightSize = $copyrightImage->getSize();

        $point = new Point($canvasSize->getWidth() - $copyrightSize->getWidth() - $this->offset,$canvasSize->getHeight() - $copyrightSize->getHeight() - $this->offset);

        $this->canvas->getImage()->paste($copyrightImage, $point);

        return $this;
    }
}
