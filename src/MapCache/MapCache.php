<?php

namespace StaticMapLite\MapCache;

use StaticMapLite\Canvas\CanvasInterface;
use StaticMapLite\Printer\PrinterInterface;

class MapCache
{
    /** @var PrinterInterface $printer */
    protected $printer;

    /** @var bool $useMapCache */
    protected $useMapCache = false;

    /** @var string $mapCacheBaseDir */
    protected $mapCacheBaseDir = '../cache/maps';

    /** @var string $mapCacheID */
    protected $mapCacheID = '';

    /** @var string $mapCacheFile */
    protected $mapCacheFile = '';

    /** @var string $mapCacheExtension */
    protected $mapCacheExtension = 'png';

    public function __construct(PrinterInterface $printer)
    {
        $this->printer = $printer;
    }

    public function checkMapCache(): bool
    {
        return false;
        $this->mapCacheID = md5($this->serializeParams());

        $filename = $this->getFilename();

        return file_exists($filename);
    }

    public function serializeParams(): string
    {
        return join('&', [
            $this->printer->getZoom(),
            $this->printer->getLatitude(),
            $this->printer->getLongitude(),
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
            $this->mapCacheFile = sprintf(
                '%s/%s/%s/cache_%/%s/%s',
                $this->mapCacheBaseDir,
                $this->printer->getMaptype(),
                $this->printer->getZoom(),
                substr($this->mapCacheID, 0, 2),
                substr($this->mapCacheID, 2, 2),
                substr($this->mapCacheID, 4)
            );
        }

        $filename = sprintf('%s.%s', $this->mapCacheFile, $this->mapCacheExtension);

        return $filename;
    }

    public function cache(CanvasInterface $canvas)
    {
        $this->mkdir_recursive(dirname($this->mapCacheIDToFilename()), 0777);
        imagepng($canvas->getImage(), $this->mapCacheIDToFilename(), 9);
        $this->sendHeader();
        if (file_exists($this->mapCacheIDToFilename())) {
            return file_get_contents($this->mapCacheIDToFilename());
        } else {
            return imagepng($canvas->getImage());
        }
    }

    public function mkdir_recursive($pathname, $mode)
    {
        is_dir(dirname($pathname)) || $this->mkdir_recursive(dirname($pathname), $mode);
        return is_dir($pathname) || @mkdir($pathname, $mode);
    }

    public function getFilename(): string
    {
        return $this->mapCacheIDToFilename();
    }

    public function sendHeader()
    {
        header('Content-Type: image/png');
        $expires = 60 * 60 * 24 * 14;
        header("Pragma: public");
        header("Cache-Control: maxage=" . $expires);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
    }

}
