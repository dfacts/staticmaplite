<?php

namespace StaticMapLite\Output;

class ImageOutput implements OutputInterface
{
    protected $image;

    public function setImage($image): OutputInterface
    {
        $this->image = $image;

        return $this;
    }

    public function sendHeader(): OutputInterface
    {
        header('Content-Type: image/png');
        $expires = 60 * 60 * 24 * 14;
        header('Pragma: public');
        header('Cache-Control: maxage=' . $expires);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');

        return $this;
    }

    public function sendImage(): bool
    {
        return imagepng($this->image);
    }
}
