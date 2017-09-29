<?php

/**
 * staticMapLite 0.3.1
 *
 * Copyright 2009 Gerhard Koch
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author Gerhard Koch <gerhard.koch AT ymail.com>
 *
 * USAGE:
 *
 *  staticmap.php?center=40.714728,-73.998672&zoom=14&size=512x512&maptype=mapnik&markers=40.702147,-74.015794,blues|40.711614,-74.012318,greeng|40.718217,-73.998284,redc
 *
 */

require_once '../vendor/autoload.php';

use StaticMapLite\Element\Marker\ExtraMarker;
use StaticMapLite\Element\Marker\Marker;
use StaticMapLite\Printer;

$printer = new Printer();

list($centerLatitude, $centerLongitude) = explode(',', $_GET['center']);
list($width, $height) = explode('x', $_GET['size']);

$printer
    ->setCenter($centerLatitude, $centerLongitude)
    ->setZoom($_GET['zoom'])
    ->setSize($width, $height)
    ->setMapType($_GET['maptype'])
;

$markers = $_GET['markers'];

if ($markers) {
    $markerList = explode('|', $markers);

    foreach ($markerList as $marker) {
        list($markerLatitude, $markerLongitude, $markerType) = explode(',', $marker);

        $marker = new ExtraMarker(ExtraMarker::SHAPE_CIRCLE, ExtraMarker::COLOR_GREEN, $markerLatitude, $markerLongitude);

        $printer->addMarker($marker);
    }
}

print $printer->showMap();
