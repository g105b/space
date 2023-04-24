<?php
use Space\Universe\Generator01UGS;

require "vendor/autoload.php";

start:
$name = bin2hex(random_bytes(32));
$name = "default";
$width = $height = 6;
$image = imagecreatetruecolor(20 * $width, 20 * $height);

for($y = -floor($height / 2); $y < floor($height / 2); $y++) {
	for($x = -floor($width / 2); $x < floor($width / 2); $x++) {
		echo "$x:$y", PHP_EOL;
		$coords = $x >= 0 ? "+$x" : $x;
		$coords .= ":";
		$coords .= $y >= 0 ? "+$y" : $y;
		$generator = new Generator01UGS("$name@ugs=$coords");
		$cMapFile = "data/universe/$name/ugs_$coords/cMap.png";
		$cMap = imagecreatefrompng($cMapFile);
		imagecopyresampled(
			$image,
			$cMap,
			($width * 10) + ($x * 20),
			($height * 10) + ($y * 20),
			0,
			0,
			20,
			20,
			200,
			200,
		);
	}
}

imagepng($image, "overview.png");
