<?php
use Gt\Input\Input;
use Space\Noise\Simplex;
use g105b\drng\Random;
use g105b\drng\StringSeed;

function go(Input $input):void {
	$xInput = $input->getInt("x") ?? 0;
	$yInput = $input->getInt("y") ?? 0;

	$width = $height = 320;
	$scale = 3;
	$image = imagecreatetruecolor($width, $height);
	$c = [];
	for($i = 0; $i < 256; $i++) {
		array_push($c, imagecolorallocate($image, $i, $i, $i));
	}

	$random = new Random(new StringSeed("Ungabi!"));

	$seed = [];
	for($i = 0; $i < 256; $i++) {
		array_push($seed, $random->getInt(0, 256));
	}
	$noise2D = new Simplex(...$seed);

	for($y = 0; $y < $height; $y++) {
		for($x = 0; $x < $width; $x++) {
			$v = $noise2D->valueAt(
				($x + ($xInput * $width)) / $width / $scale,
				($y + ($yInput * $height)) / $height / $scale,
			);

			$v = floor(($v + 1) * 128);
			$v = min($v, count($c) - 1);
			$v = max($v, 0);
			imagesetpixel($image, $x, $y, $c[$v]);
		}
	}

	header("x-coords: $xInput, $yInput");
	header("content-type: image/png");
	imagepng($image);
	exit;
}
