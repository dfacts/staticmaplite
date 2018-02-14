<?php

namespace StaticMapLite\Output;

class CacheOutput implements OutputInterface
{
    protected $filename;

    public function setFilename($filename): OutputInterface
    {
        $this->filename = $filename;

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

    public function sendImage(): void
    {
        echo file_get_contents($this->filename);
    }
}
