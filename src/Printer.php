<?php

namespace StaticMapLite;

class Printer
{
    protected $maxWidth = 1024;
    protected $maxHeight = 1024;

    protected $tileSize = 256;
    protected $tileSrcUrl = array('mapnik' => 'http://tile.openstreetmap.org/{Z}/{X}/{Y}.png',
        'osmarenderer' => 'http://otile1.mqcdn.com/tiles/1.0.0/osm/{Z}/{X}/{Y}.png',
        'cycle' => 'http://a.tile.opencyclemap.org/cycle/{Z}/{X}/{Y}.png',
        'wikimedia-intl' => 'https://maps.wikimedia.org/osm-intl/{Z}/{X}/{Y}.png',
    );

    protected $tileDefaultSrc = 'mapnik';
    protected $markerBaseDir = 'images/markers';
    protected $osmLogo = 'images/osm_logo.png';

    protected $markerPrototypes = array(
        // found at http://www.mapito.net/map-marker-icons.html
        'lighblue' => array('regex' => '/^lightblue([0-9]+)$/',
            'extension' => '.png',
            'shadow' => false,
            'offsetImage' => '0,-19',
            'offsetShadow' => false
        ),
        // openlayers std markers
        'ol-marker' => array('regex' => '/^ol-marker(|-blue|-gold|-green)+$/',
            'extension' => '.png',
            'shadow' => '../marker_shadow.png',
            'offsetImage' => '-10,-25',
            'offsetShadow' => '-1,-13'
        ),
        // taken from http://www.visual-case.it/cgi-bin/vc/GMapsIcons.pl
        'ylw' => array('regex' => '/^(pink|purple|red|ltblu|ylw)-pushpin$/',
            'extension' => '.png',
            'shadow' => '../marker_shadow.png',
            'offsetImage' => '-10,-32',
            'offsetShadow' => '-1,-13'
        ),
        // http://svn.openstreetmap.org/sites/other/StaticMap/symbols/0.png
        'ojw' => array('regex' => '/^bullseye$/',
            'extension' => '.png',
            'shadow' => false,
            'offsetImage' => '-20,-20',
            'offsetShadow' => false
        )
    );


    protected $useTileCache = true;
    protected $tileCacheBaseDir = '../cache/tiles';

    protected $useMapCache = false;
    protected $mapCacheBaseDir = '../cache/maps';
    protected $mapCacheID = '';
    protected $mapCacheFile = '';
    protected $mapCacheExtension = 'png';

    protected $zoom, $lat, $lon, $width, $height, $image, $maptype;
    protected $centerX, $centerY, $offsetX, $offsetY;

    protected $markers = [];
    protected $polylines = [];

    public function __construct()
    {
        $this->zoom = 0;
        $this->lat = 0;
        $this->lon = 0;
        $this->width = 500;
        $this->height = 350;
        $this->maptype = $this->tileDefaultSrc;
    }

    public function addMarker(string $markerType, float $latitude, float $longitude): Printer
    {
        $marker = ['lat' => $latitude, 'lon' => $longitude, 'type' => $markerType];

        array_push($this->markers, $marker);

        return $this;
    }

    public function addPolyline(string $polylineString, int $colorRed, int $colorGreen, int $colorBlue): Printer
    {
        $polyline = ['polyline' => $polylineString, 'colorRed' => $colorRed, 'colorGreen' => $colorGreen, 'colorBlue' => $colorBlue];

        array_push($this->polylines, $polyline);

        return $this;
    }

    public function setCenter(float $latitude, float $longitude): Printer
    {
        $this->lat = $latitude;
        $this->lon = $longitude;

        return $this;
    }

    public function setSize(int $width, int $height): Printer
    {
        $this->width = $width;
        $this->height = $height;

        if ($this->width > $this->maxWidth) {
            $this->width = $this->maxWidth;
        }

        if ($this->height > $this->maxHeight) {
            $this->height = $this->maxHeight;
        }

        return $this;
    }

    public function setZoom(int $zoom): Printer
    {
        $this->zoom = $zoom;

        if ($this->zoom > 18) {
            $this->zoom = 18;
        }

        return $this;
    }

    public function setMapType(string $mapType): Printer
    {
        $this->maptype = $mapType;

        return $this;
    }

    public function lonToTile($long, $zoom)
    {
        return (($long + 180) / 360) * pow(2, $zoom);
    }

    public function latToTile($lat, $zoom)
    {
        return (1 - log(tan($lat * pi() / 180) + 1 / cos($lat * pi() / 180)) / pi()) / 2 * pow(2, $zoom);
    }

    public function initCoords()
    {
        $this->centerX = $this->lonToTile($this->lon, $this->zoom);
        $this->centerY = $this->latToTile($this->lat, $this->zoom);
        $this->offsetX = floor((floor($this->centerX) - $this->centerX) * $this->tileSize);
        $this->offsetY = floor((floor($this->centerY) - $this->centerY) * $this->tileSize);
    }

    public function createBaseMap()
    {
        $this->image = imagecreatetruecolor($this->width, $this->height);
        $startX = floor($this->centerX - ($this->width / $this->tileSize) / 2);
        $startY = floor($this->centerY - ($this->height / $this->tileSize) / 2);
        $endX = ceil($this->centerX + ($this->width / $this->tileSize) / 2);
        $endY = ceil($this->centerY + ($this->height / $this->tileSize) / 2);
        $this->offsetX = -floor(($this->centerX - floor($this->centerX)) * $this->tileSize);
        $this->offsetY = -floor(($this->centerY - floor($this->centerY)) * $this->tileSize);
        $this->offsetX += floor($this->width / 2);
        $this->offsetY += floor($this->height / 2);
        $this->offsetX += floor($startX - floor($this->centerX)) * $this->tileSize;
        $this->offsetY += floor($startY - floor($this->centerY)) * $this->tileSize;

        for ($x = $startX; $x <= $endX; $x++) {
            for ($y = $startY; $y <= $endY; $y++) {
                $url = str_replace(array('{Z}', '{X}', '{Y}'), array($this->zoom, $x, $y), $this->tileSrcUrl[$this->maptype]);
                $tileData = $this->fetchTile($url);
                if ($tileData) {
                    $tileImage = imagecreatefromstring($tileData);
                } else {
                    $tileImage = imagecreate($this->tileSize, $this->tileSize);
                    $color = imagecolorallocate($tileImage, 255, 255, 255);
                    @imagestring($tileImage, 1, 127, 127, 'err', $color);
                }
                $destX = ($x - $startX) * $this->tileSize + $this->offsetX;
                $destY = ($y - $startY) * $this->tileSize + $this->offsetY;
                imagecopy($this->image, $tileImage, $destX, $destY, 0, 0, $this->tileSize, $this->tileSize);
            }
        }
    }


    public function placeMarkers()
    {
        // loop thru marker array
        foreach ($this->markers as $marker) {
            // set some local variables
            $markerLat = $marker['lat'];
            $markerLon = $marker['lon'];
            $markerType = $marker['type'];
            // clear variables from previous loops
            $markerFilename = '';
            $markerShadow = '';
            $matches = false;
            // check for marker type, get settings from markerPrototypes
            if ($markerType) {
                foreach ($this->markerPrototypes as $markerPrototype) {
                    if (preg_match($markerPrototype['regex'], $markerType, $matches)) {
                        $markerFilename = $matches[0] . $markerPrototype['extension'];
                        if ($markerPrototype['offsetImage']) {
                            list($markerImageOffsetX, $markerImageOffsetY) = explode(",", $markerPrototype['offsetImage']);
                        }
                        $markerShadow = $markerPrototype['shadow'];
                        if ($markerShadow) {
                            list($markerShadowOffsetX, $markerShadowOffsetY) = explode(",", $markerPrototype['offsetShadow']);
                        }
                    }

                }
            }

            // check required files or set default
            if ($markerFilename == '' || !file_exists($this->markerBaseDir . '/' . $markerFilename)) {
                $markerIndex++;
                $markerFilename = 'lightblue' . $markerIndex . '.png';
                $markerImageOffsetX = 0;
                $markerImageOffsetY = -19;
            }

            // create img resource
            if (file_exists($this->markerBaseDir . '/' . $markerFilename)) {
                $markerImg = imagecreatefrompng($this->markerBaseDir . '/' . $markerFilename);
            } else {
                $markerImg = imagecreatefrompng($this->markerBaseDir . '/lightblue1.png');
            }

            // check for shadow + create shadow recource
            if ($markerShadow && file_exists($this->markerBaseDir . '/' . $markerShadow)) {
                $markerShadowImg = imagecreatefrompng($this->markerBaseDir . '/' . $markerShadow);
            }

            // calc position
            $destX = floor(($this->width / 2) - $this->tileSize * ($this->centerX - $this->lonToTile($markerLon, $this->zoom)));
            $destY = floor(($this->height / 2) - $this->tileSize * ($this->centerY - $this->latToTile($markerLat, $this->zoom)));

            // copy shadow on basemap
            if ($markerShadow && $markerShadowImg) {
                imagecopy($this->image, $markerShadowImg, $destX + intval($markerShadowOffsetX), $destY + intval($markerShadowOffsetY),
                    0, 0, imagesx($markerShadowImg), imagesy($markerShadowImg));
            }

            // copy marker on basemap above shadow
            imagecopy($this->image, $markerImg, $destX + intval($markerImageOffsetX), $destY + intval($markerImageOffsetY),
                0, 0, imagesx($markerImg), imagesy($markerImg));

        };
    }

    public function placePolylines()
    {
        // loop thru marker array
        foreach ($this->polylines as $polyline) {
            // set some local variables
            $polylineString = $polyline['polyline'];
            $colorRed = $polyline['colorRed'];
            $colorGreen = $polyline['colorGreen'];
            $colorBlue = $polyline['colorBlue'];

            $polylineList = \Polyline::decode($polylineString);

            $sourceLatitude = null;
            $sourceLongitude = null;
            $destinationLatitude = null;
            $destinationLongitude = null;

            $color = imagecolorallocate($this->image, $colorRed, $colorGreen, $colorBlue);
            imagesetthickness($this->image, 3);
            //imageantialias($this->image, true);

            while (!empty($polylineList)) {
                if (!$sourceLatitude) {
                    $sourceLatitude = array_shift($polylineList);
                }

                if (!$sourceLongitude) {
                    $sourceLongitude = array_shift($polylineList);
                }

                $sourceX = floor(($this->width / 2) - $this->tileSize * ($this->centerX - $this->lonToTile($sourceLongitude, $this->zoom)));
                $sourceY = floor(($this->height / 2) - $this->tileSize * ($this->centerY - $this->latToTile($sourceLatitude, $this->zoom)));

                $destinationLatitude = array_shift($polylineList);
                $destinationLongitude = array_shift($polylineList);

                $destinationX = floor(($this->width / 2) - $this->tileSize * ($this->centerX - $this->lonToTile($destinationLongitude, $this->zoom)));
                $destinationY = floor(($this->height / 2) - $this->tileSize * ($this->centerY - $this->latToTile($destinationLatitude, $this->zoom)));

                imageline($this->image , $sourceX, $sourceY , $destinationX, $destinationY, $color);

                $sourceLatitude = $destinationLatitude;
                $sourceLongitude = $destinationLongitude;
            }
        }
    }

    public function tileUrlToFilename($url)
    {
        return $this->tileCacheBaseDir . "/" . str_replace(array('http://'), '', $url);
    }

    public function checkTileCache($url)
    {
        $filename = $this->tileUrlToFilename($url);
        if (file_exists($filename)) {
            return file_get_contents($filename);
        }
    }

    public function checkMapCache()
    {
        $this->mapCacheID = md5($this->serializeParams());
        $filename = $this->mapCacheIDToFilename();
        if (file_exists($filename)) return true;
    }

    public function serializeParams()
    {
        return join("&", array($this->zoom, $this->lat, $this->lon, $this->width, $this->height, serialize($this->markers), $this->maptype));
    }

    public function mapCacheIDToFilename()
    {
        if (!$this->mapCacheFile) {
            $this->mapCacheFile = $this->mapCacheBaseDir . "/" . $this->maptype . "/" . $this->zoom . "/cache_" . substr($this->mapCacheID, 0, 2) . "/" . substr($this->mapCacheID, 2, 2) . "/" . substr($this->mapCacheID, 4);
        }
        return $this->mapCacheFile . "." . $this->mapCacheExtension;
    }


    public function mkdir_recursive($pathname, $mode)
    {
        is_dir(dirname($pathname)) || $this->mkdir_recursive(dirname($pathname), $mode);
        return is_dir($pathname) || @mkdir($pathname, $mode);
    }

    public function writeTileToCache($url, $data)
    {
        $filename = $this->tileUrlToFilename($url);
        $this->mkdir_recursive(dirname($filename), 0777);
        file_put_contents($filename, $data);
    }

    public function fetchTile($url)
    {
        if ($this->useTileCache && ($cached = $this->checkTileCache($url))) return $cached;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0");
        curl_setopt($ch, CURLOPT_URL, $url);
        $tile = curl_exec($ch);
        curl_close($ch);
        if ($tile && $this->useTileCache) {
            $this->writeTileToCache($url, $tile);
        }
        return $tile;

    }

    public function copyrightNotice()
    {
        $logoImg = imagecreatefrompng($this->osmLogo);
        imagecopy($this->image, $logoImg, imagesx($this->image) - imagesx($logoImg), imagesy($this->image) - imagesy($logoImg), 0, 0, imagesx($logoImg), imagesy($logoImg));

    }

    public function sendHeader()
    {
        header('Content-Type: image/png');
        $expires = 60 * 60 * 24 * 14;
        header("Pragma: public");
        header("Cache-Control: maxage=" . $expires);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
    }

    public function makeMap()
    {
        $this->initCoords();
        $this->createBaseMap();
        if (count($this->markers)) $this->placeMarkers();
        if (count($this->polylines)) $this->placePolylines();
        if ($this->osmLogo) $this->copyrightNotice();
    }

    public function showMap()
    {
        if ($this->useMapCache) {
            // use map cache, so check cache for map
            if (!$this->checkMapCache()) {
                // map is not in cache, needs to be build
                $this->makeMap();
                $this->mkdir_recursive(dirname($this->mapCacheIDToFilename()), 0777);
                imagepng($this->image, $this->mapCacheIDToFilename(), 9);
                $this->sendHeader();
                if (file_exists($this->mapCacheIDToFilename())) {
                    return file_get_contents($this->mapCacheIDToFilename());
                } else {
                    return imagepng($this->image);
                }
            } else {
                // map is in cache
                $this->sendHeader();
                return file_get_contents($this->mapCacheIDToFilename());
            }

        } else {
            // no cache, make map, send headers and deliver png
            $this->makeMap();
            $this->sendHeader();
            return imagepng($this->image);

        }
    }
}
