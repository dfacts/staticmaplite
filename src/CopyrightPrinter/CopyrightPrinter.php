<?php

namespace StaticMapLite\CopyrightPrinter;

use StaticMapLite\Canvas\CanvasInterface;

class CopyrightPrinter implements CopyrightPrinterInterface
{
    /** @var string $osmLogo */
    protected $osmLogo = '../images/osm_logo.png';

    /** @var CanvasInterface $canvas */
    protected $canvas;

    public function setCanvas(CanvasInterface $canvas): CopyrightPrinterInterface
    {
        $this->canvas = $canvas;

        return $this;
    }

    public function printCopyright(): CopyrightPrinterInterface
    {
        $logo = imagecreatefrompng($this->osmLogo);

        $logoWidth = imagesx($logo);
        $logoHeight = imagesy($logo);

        imagecopy(
            $this->canvas->getImage(),
            $logo,
            imagesx($this->canvas->getImage()) - $logoWidth,
            imagesy($this->canvas->getImage()) - $logoHeight,
            0,
            0,
            $logoWidth,
            $logoHeight
        );

        return $this;
    }
}
