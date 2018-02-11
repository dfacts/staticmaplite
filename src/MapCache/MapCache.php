<?php

namespace StaticMapLite\MapCache;

use StaticMapLite\Printer;

class MapCache
{
    /** @var Printer $printer */
    protected $printer;

    public function __construct(Printer $printer)
    {

    }

    public function checkMapCache(): bool
    {
        $this->mapCacheID = md5($this->serializeParams());
        $filename = $this->mapCacheIDToFilename();

        return file_exists($filename);
    }

    public function serializeParams(): string
    {
        return join('&', [
            $this->printer->getZoom(),
            $this->printer->getCenterLatitude(),
            $this->printer->getCenterLongitude(),
            $this->printer->getWidth(),
            $this->printer->getHeight(),
            serialize($this->printer->getMarkers()),
            serialize($this->printer->getPolylines()),
            $this->printer->getMapType()
        ]);
    }

    public function mapCacheIDToFilename()
    {
        if (!$this->mapCacheFile) {
            $this->mapCacheFile = $this->mapCacheBaseDir . "/" . $this->maptype . "/" . $this->zoom . "/cache_" . substr($this->mapCacheID, 0, 2) . "/" . substr($this->mapCacheID, 2, 2) . "/" . substr($this->mapCacheID, 4);
        }
        return $this->mapCacheFile . "." . $this->mapCacheExtension;
    }
}
