<?php

/**
 * staticMapLite 0.03
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

error_reporting(0);
ini_set('display_errors','off');

Class staticMapLite {

	protected $maxWidth = 1024;
	protected $maxHeight = 1024;

	protected $tileSize = 256;
	protected $tileSrcUrl = array(	'mapnik' => 'http://tile.openstreetmap.org/{Z}/{X}/{Y}.png',
									'osmarenderer' => 'http://c.tah.openstreetmap.org/Tiles/tile/{Z}/{X}/{Y}.png',
									'cycle' => 'http://c.andy.sandbox.cloudmade.com/tiles/cycle/{Z}/{X}/{Y}.png',
									'piste' => 'http://openpistemap.org/tiles/contours/{Z}/{X}/{Y}.png',
									'topo' => 'http://topo.geofabrik.de/trails/{Z}/{X}/{Y}.png'

	);
	
	protected $tileDefaultSrc = 'mapnik';
	protected $markerBaseDir = 'images/markers';
	protected $osmLogo = 'images/osm_logo.png';
	
	protected $markerPrototypes = array(// found at http://www.mapito.net/map-marker-icons.html
										'lighblue' => array('regex'=>'/^lightblue([0-9]+)$/', 
															'extension'=>'.png',
															'shadow'=>false, 
															'offsetImage'=>'0,-19',
															'offsetShadow'=>false
														),
										// openlayers std markers
										'ol-marker'=> array('regex'=>'/^ol-marker(|-blue|-gold|-green)+$/',
															'extension'=>'.png',
															'shadow'=>'../marker_shadow.png', 
															'offsetImage'=>'-10,-25', 
															'offsetShadow'=>'-1,-13'
														),
										// taken from http://www.visual-case.it/cgi-bin/vc/GMapsIcons.pl
										'ylw'=> array('regex'=>'/^(pink|purple|red|ltblu|ylw)-pushpin$/',
															'extension'=>'.png',
															'shadow'=>'../marker_shadow.png', 
															'offsetImage'=>'-10,-32', 
															'offsetShadow'=>'-1,-13'
														)
														
									);

	

	protected $useTileCache = true;
	protected $tileCacheBaseDir = 'cache/tiles';

	protected $useMapCache = true;
	protected $mapCacheBaseDir = 'cache/maps';
	protected $mapCacheID = '';
	protected $mapCacheFile = '';
	protected $mapCacheExtension = 'png';
	
	protected $zoom, $lat, $lon, $width, $height, $markers, $image, $maptype;
	protected $centerX, $centerY, $offsetX, $offsetY;

	public function __construct(){
		$this->zoom = 0;
		$this->lat = 0;
		$this->lon = 0;
		$this->width = 500;
		$this->height = 350;
		$this->markers = array();
		$this->maptype = $this->tileDefaultSrc;
	}
	
	public function parseParams(){
		global $_GET;
		
		// get zoom from GET paramter
		$this->zoom = $_GET['zoom']?intval($_GET['zoom']):0;
		if($this->zoom>18)$this->zoom = 18;
		
		// get lat and lon from GET paramter
		list($this->lat,$this->lon) = split(',',$_GET['center']);
		$this->lat = floatval($this->lat);
		$this->lon = floatval($this->lon);
		
		// get zoom from GET paramter
		if($_GET['size']){
			list($this->width, $this->height) = split('x',$_GET['size']);
			$this->width = intval($this->width);
			if($this->width > $this->maxWidth) $this->width = $this->maxWidth;
			$this->height = intval($this->height);
			if($this->height > $this->maxHeight) $this->height = $this->maxHeight;
		}
		if($_GET['markers']){
			$markers = split('%7C|\|',$_GET['markers']);
			foreach($markers as $marker){
					list($markerLat, $markerLon, $markerType) = split(',',$marker);
					$markerLat = floatval($markerLat);
					$markerLon = floatval($markerLon);
					$markerType = basename($markerType);
					$this->markers[] = array('lat'=>$markerLat, 'lon'=>$markerLon, 'type'=>$markerType);
			}
			
		}
		if($_GET['maptype']){
			if(array_key_exists($_GET['maptype'],$this->tileSrcUrl)) $this->maptype = $_GET['maptype'];
		}
	}

	public function lonToTile($long, $zoom){
		return (($long + 180) / 360) * pow(2, $zoom);
	}

	public function latToTile($lat, $zoom){
		return (1 - log(tan($lat * pi()/180) + 1 / cos($lat* pi()/180)) / pi()) /2 * pow(2, $zoom);
	}

	public function initCoords(){
		$this->centerX = $this->lonToTile($this->lon, $this->zoom);
		$this->centerY = $this->latToTile($this->lat, $this->zoom);
		$this->offsetX = floor((floor($this->centerX)-$this->centerX)*$this->tileSize);
		$this->offsetY = floor((floor($this->centerY)-$this->centerY)*$this->tileSize);
	}

	public function createBaseMap(){
		$this->image = imagecreatetruecolor($this->width, $this->height);
		$startX = floor($this->centerX-($this->width/$this->tileSize)/2);
		$startY = floor($this->centerY-($this->height/$this->tileSize)/2);
		$endX = ceil($this->centerX+($this->width/$this->tileSize)/2);
		$endY = ceil($this->centerY+($this->height/$this->tileSize)/2);
		$this->offsetX = -floor(($this->centerX-floor($this->centerX))*$this->tileSize);
		$this->offsetY = -floor(($this->centerY-floor($this->centerY))*$this->tileSize);
		$this->offsetX += floor($this->width/2);
		$this->offsetY += floor($this->height/2);
		$this->offsetX += floor($startX-floor($this->centerX))*$this->tileSize;
		$this->offsetY += floor($startY-floor($this->centerY))*$this->tileSize;

		for($x=$startX; $x<=$endX; $x++){
			for($y=$startY; $y<=$endY; $y++){
				$url = str_replace(array('{Z}','{X}','{Y}'),array($this->zoom, $x, $y), $this->tileSrcUrl[$this->maptype]);
				$tileData = $this->fetchTile($url);
				if($tileData){
					$tileImage = imagecreatefromstring($tileData);
				} else {
					$tileImage = imagecreate($this->tileSize,$this->tileSize);
					$color = imagecolorallocate($tileImage, 255, 255, 255);
					@imagestring($tileImage,1,127,127,'err',$color);
				}
				$destX = ($x-$startX)*$this->tileSize+$this->offsetX;
				$destY = ($y-$startY)*$this->tileSize+$this->offsetY;
				imagecopy($this->image, $tileImage, $destX, $destY, 0, 0, $this->tileSize, $this->tileSize);
			}
		}
	}


	public function placeMarkers(){
		// loop thru marker array
		foreach($this->markers as $marker){
			// set some local variables
			$markerLat = $marker['lat'];
			$markerLon = $marker['lon'];
			$markerType = $marker['type'];
			// clear variables from previous loops
			$markerFilename = '';
			$markerShadow = '';
			$matches = false;
			// check for marker type, get settings from markerPrototypes
			if($markerType){
				foreach($this->markerPrototypes as $markerPrototype){ 
					if(preg_match($markerPrototype['regex'],$markerType,$matches)){
						$markerFilename = $matches[0].$markerPrototype['extension'];
						if($markerPrototype['offsetImage']){
							list($markerImageOffsetX, $markerImageOffsetY)  = split(",",$markerPrototype['offsetImage']);
						}
						$markerShadow = $markerPrototype['shadow'];
						if($markerShadow){
							list($markerShadowOffsetX, $markerShadowOffsetY)  = split(",",$markerPrototype['offsetShadow']);
						} 
					}
				}
			}

			// check required files or set default
			if($markerFilename == '' || !file_exists($this->markerBaseDir.'/'.$markerFilename)){
				$markerIndex++;
				$markerFilename = 'lightblue'.$markerIndex.'.png';
				$markerImageOffsetX = 0;
				$markerImageOffsetY = -19;					}
			
			// create img resource
			if(file_exists($this->markerBaseDir.'/'.$markerFilename)){
				$markerImg = imagecreatefrompng($this->markerBaseDir.'/'.$markerFilename);
			} else {
				$markerImg = imagecreatefrompng($this->markerBaseDir.'/lightblue1.png');				
			}
			
			// check for shadow + create shadow recource
			if($markerShadow && file_exists($this->markerBaseDir.'/'.$markerShadow)){
				$markerShadowImg = imagecreatefrompng($this->markerBaseDir.'/'.$markerShadow);
			}

			// calc position
			$destX = floor(($this->width/2)-$this->tileSize*($this->centerX-$this->lonToTile($markerLon, $this->zoom)));
			$destY = floor(($this->height/2)-$this->tileSize*($this->centerY-$this->latToTile($markerLat, $this->zoom)));

			// copy shadow on basemap
			if($markerShadow && $markerShadowImg){
				imagecopy($this->image, $markerShadowImg, $destX+intval($markerShadowOffsetX), $destY+intval($markerShadowOffsetY), 
							0, 0, imagesx($markerShadowImg), imagesy($markerShadowImg));
			}
			
			// copy marker on basemap above shadow
			imagecopy($this->image, $markerImg, $destX+intval($markerImageOffsetX), $destY+intval($markerImageOffsetY), 
							0, 0, imagesx($markerImg), imagesy($markerImg));
		
	};
}



	public function tileUrlToFilename($url){
		return $this->tileCacheBaseDir."/".str_replace(array('http://'),'',$url);
	}

	public function checkTileCache($url){
		$filename = $this->tileUrlToFilename($url);
		if(file_exists($filename)){
			return file_get_contents($filename);
		}
	}
	
	public function checkMapCache(){
		$this->mapCacheID = md5($this->serializeParams());
		$filename = $this->mapCacheIDToFilename();
		if(file_exists($filename)) return true;
	}

	public function serializeParams(){		
		return join("&",array($this->zoom,$this->lat,$this->lon,$this->width,$this->height, serialize($this->markers),$this->maptype));
	}
	
	public function mapCacheIDToFilename(){
		if(!$this->mapCacheFile){
			$this->mapCacheFile = $this->mapCacheBaseDir."/".$this->maptype."/".$this->zoom."/cache_".substr($this->mapCacheID,0,2)."/".substr($this->mapCacheID,2,2)."/".substr($this->mapCacheID,4);
		}
		return $this->mapCacheFile.".".$this->mapCacheExtension;
	}


	
	public function mkdir_recursive($pathname, $mode){
		is_dir(dirname($pathname)) || $this->mkdir_recursive(dirname($pathname), $mode);
		return is_dir($pathname) || @mkdir($pathname, $mode);
	}
	public function writeTileToCache($url, $data){
		$filename = $this->tileUrlToFilename($url);
		$this->mkdir_recursive(dirname($filename),0777);
		file_put_contents($filename, $data);
	}
	
	public function fetchTile($url){
		if($this->useTileCache && ($cached = $this->checkTileCache($url))) return $cached;
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0");
		curl_setopt($ch, CURLOPT_URL, $url); 
		$tile = curl_exec($ch); 
		curl_close($ch); 
		if($tile && $this->useTileCache){
			$this->writeTileToCache($url,$tile);
		}
		return $tile;

	}

	public function copyrightNotice(){
			$logoImg = imagecreatefrompng($this->osmLogo);
			imagecopy($this->image, $logoImg, imagesx($this->image)-imagesx($logoImg), imagesy($this->image)-imagesy($logoImg), 0, 0, imagesx($logoImg), imagesy($logoImg));
		
	}
	
	public function sendHeader(){
		header('Content-Type: image/png');
		$expires = 60*60*24*14;
		header("Pragma: public");
		header("Cache-Control: maxage=".$expires);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
	}

	public function makeMap(){
		$this->initCoords();		
		$this->createBaseMap();
		if(count($this->markers))$this->placeMarkers();
		if($this->osmLogo) $this->copyrightNotice();
	}

	public function showMap(){
		$this->parseParams();
		if($this->useMapCache){
			// use map cache, so check cache for map
			if(!$this->checkMapCache()){
				// map is not in cache, needs to be build
				$this->makeMap();
				$this->mkdir_recursive(dirname($this->mapCacheIDToFilename()),0777);
				imagepng($this->image,$this->mapCacheIDToFilename(),9);
				$this->sendHeader();	
				if(file_exists($this->mapCacheIDToFilename())){
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

$map = new staticMapLite();
print $map->showMap();

?>