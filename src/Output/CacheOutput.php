<?php

namespace StaticMapLite\Output;

class CacheOutput extends AbstractOutput
{
    protected $filename;

    public function setFilename($filename): OutputInterface
    {
        $this->filename = $filename;

        return $this;
    }

    public function sendImage(): void
    {
        echo file_get_contents($this->filename);
    }
}
