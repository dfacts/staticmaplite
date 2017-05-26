<?php

namespace StaticMapLite\Canvas;

class Canvas
{
    protected $image = null;
    protected $width = 0;
    protected $height = 0;

    public function __construct(int $width, int $height)
    {
        $this->width = $width;
        $this->height = $height;

        $this->image = imagecreatetruecolor($this->width, $this->height);
    }

    public function getImage()
    {
        return $this->image;
    }
}
