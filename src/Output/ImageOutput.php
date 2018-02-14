<?php

namespace StaticMapLite\Output;

class ImageOutput extends AbstractOutput
{
    protected $image;

    public function setImage($image): OutputInterface
    {
        $this->image = $image;

        return $this;
    }

    public function sendImage(): bool
    {
        return imagepng($this->image);
    }
}
