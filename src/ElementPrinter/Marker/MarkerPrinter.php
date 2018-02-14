<?php

namespace StaticMapLite\ElementPrinter\Marker;

use StaticMapLite\Canvas\Canvas;
use StaticMapLite\Element\Marker\AbstractMarker;
use StaticMapLite\Element\Marker\Marker;
use StaticMapLite\Util;

/** @deprecated */
class MarkerPrinter
{
    /** @var Marker $marker */
    protected $marker = null;

    protected $markerBaseDir = '../images/markers';

    protected $markerPrototypes = array(
        // found at http://www.mapito.net/map-marker-icons.html
        'lighblue' => array('regex' => '/^lightblue([0-9]+)$/',
            'extension' => '.png',
            'shadow' => false,
            'offsetImage' => '0,-19',
            'offsetShadow' => false,
        ),
        // openlayers std markers
        'ol-marker' => array('regex' => '/^ol-marker(|-blue|-gold|-green)+$/',
            'extension' => '.png',
            'shadow' => '../marker_shadow.png',
            'offsetImage' => '-10,-25',
            'offsetShadow' => '-1,-13',
        ),
        // taken from http://www.visual-case.it/cgi-bin/vc/GMapsIcons.pl
        'ylw' => array('regex' => '/^(pink|purple|red|ltblu|ylw)-pushpin$/',
            'extension' => '.png',
            'shadow' => '../marker_shadow.png',
            'offsetImage' => '-10,-32',
            'offsetShadow' => '-1,-13',
        ),
        // http://svn.openstreetmap.org/sites/other/StaticMap/symbols/0.png
        'ojw' => array('regex' => '/^bullseye$/',
            'extension' => '.png',
            'shadow' => false,
            'offsetImage' => '-20,-20',
            'offsetShadow' => false,
        ),
    );

    public function __construct()
    {

    }

    public function setMarker(AbstractMarker $marker): MarkerPrinter
    {
        $this->marker = $marker;

        return $this;
    }

    public function paint(Canvas $canvas): MarkerPrinter
    {
        $markerFilename = '';
        $markerShadow = '';
        $matches = false;
        $markerIndex = 0;

        // check for marker type, get settings from markerPrototypes
        if ($this->marker->getMarkerType()) {
            foreach ($this->markerPrototypes as $markerPrototype) {
                if (preg_match($markerPrototype['regex'], $this->marker->getMarkerType(), $matches)) {
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
        $destX = floor(($canvas->getWidth() / 2) - $canvas->getTileSize() * ($canvas->getCenterX() - Util::lonToTile($this->marker->getLongitude(), $canvas->getZoom())));
        $destY = floor(($canvas->getHeight() / 2) - $canvas->getTileSize() * ($canvas->getCenterY() - Util::latToTile($this->marker->getLatitude(), $canvas->getZoom())));

        // copy shadow on basemap
        if ($markerShadow && $markerShadowImg) {
            imagecopy($canvas->getImage(), $markerShadowImg, $destX + intval($markerShadowOffsetX), $destY + intval($markerShadowOffsetY),
                0, 0, imagesx($markerShadowImg), imagesy($markerShadowImg));
        }

        // copy marker on basemap above shadow
        imagecopy($canvas->getImage(), $markerImg, $destX + intval($markerImageOffsetX), $destY + intval($markerImageOffsetY),
            0, 0, imagesx($markerImg), imagesy($markerImg));


        return $this;
    }

}
