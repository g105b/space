<?php
use Space\Universe\Generator01UGS;
use Space\Universe\Generator02FGS;
use Space\Universe\Generator03GGS;
use Space\Universe\Generator04SGS;

require "vendor/autoload.php";

$config = parse_ini_file("config.ini", true);
if(file_exists("config.dev.ini")) {
	$config = array_merge($config, parse_ini_file("config.dev.ini", true));
}
if(file_exists("config.production.ini")) {
	$config = array_merge($config, parse_ini_file("config.production.ini", true));
}

$name = $config["app"]["universe_id"];

$ugsCoords = "+0:+0";
$ugsGenerator = new Generator01UGS("$name@ugs=$ugsCoords", true);
//$fgsCoords = "+14:-15";
$fgsCoords = "+0:+0";
$fgsGenerator = new Generator02FGS("$name@ugs=$ugsCoords fgs=$fgsCoords", false);
$ggsCoords = "+0:+0";
$ggsGenerator = new Generator03GGS("$name@ugs=$ugsCoords fgs=$fgsCoords ggs=$ggsCoords", false);
//$sgsCoords = "+0:+0";
$sgsCoords = "+1:+1";
$sgsGenerator = new Generator04SGS("$name@ugs=$ugsCoords fgs=$fgsCoords ggs=$ggsCoords sgs=$sgsCoords", false);
exit;
ugs:

$ugsWidth = $ugsHeight = 2;

$lgsWidth = 2;
$lgsHeight = 2;

$image = imagecreatetruecolor(20 * $ugsWidth, 20 * $ugsHeight);

for($ucsY = -floor($ugsHeight / 2); $ucsY < floor($ugsHeight / 2); $ucsY++) {
	for($ugsX = -floor($ugsWidth / 2); $ugsX < floor($ugsWidth / 2); $ugsX++) {
		$ugsCoords = $ugsX >= 0 ? "+$ugsX" : $ugsX;
		$ugsCoords .= ":";
		$ugsCoords .= $ucsY >= 0 ? "+$ucsY" : $ucsY;
		echo "UGS $ugsCoords", PHP_EOL;
		$ugsGenerator = new Generator01UGS("$name@ugs=$ugsCoords", true);
		$ugsMapFile = "data/universe/$name/ugs_$ugsCoords/cMap.png";
		$ugsMap = imagecreatefrompng($ugsMapFile);

		for($lgsY = -floor($lgsHeight / 2); $lgsY < floor($lgsHeight / 2); $lgsY++) {
			for($lgsX = -floor($lgsWidth / 2); $lgsX < floor($lgsWidth / 2); $lgsX++) {
				$fgsCoords = $lgsX >= 0 ? "+$lgsX" : $lgsX;
//				$lgsCoords = "+27";
				$fgsCoords .= ":";
				$fgsCoords .= $lgsY >= 0 ? "+$lgsY" : $lgsY;
//				$lgsCoords .= "-54";
				echo "\tFGS $fgsCoords", PHP_EOL;

				$lgsGenerator = new Generator02FGS("$name@ugs=$ugsCoords fgs=$fgsCoords", false);

			}
		}
//		imagecopyresampled(
//			$image,
//			$cMap,
//			($width * 10) + ($ugsX * 20),
//			($height * 10) + ($ucsY * 20),
//			0,
//			0,
//			20,
//			20,
//			200,
//			200,
//		);
	}
}

//imagepng($image, "overview.png");
