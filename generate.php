<?php
use Space\Universe\Generator01UGS;

require "vendor/autoload.php";

$width = $height = 510;
$image = imagecreatetruecolor(20 * $width, 20 * $height);

for($y = 0; $y < $height; $y++) {
	for($x = 0; $x < $width; $x++) {
		$coords = $x >= 0 ? "+$x" : $x;
		$coords .= ":";
		$coords .= $y >= 0 ? "+$y" : $y;
		$generator = new Generator01UGS("default@ugs=$coords");
		$cMapFile = "data/universe/default/ugs_$coords/cMap.png";
		$cMap = imagecreatefrompng($cMapFile);
		imagecopyresampled($image, $cMap, $x * 20, $y * 20, 0, 0, 20, 20, 200, 200);
	}
}

imagepng($image, "overview.png");
