<?php
namespace Space\Universe;

use Space\Noise\Simplex;

class Generator02FGS extends AbstractUniverseGenerator {
	public const CODE = "fgs";
	public const REGEX = Generator01UGS::REGEX . " fgs=(?P<FGS_X>[+-]\d+):(?P<FGS_Y>[+-]\d+)";
	public const MIN = -64;
	public const MAX = +64;

	protected function generate():array {
		$noiseBrightness = new Simplex(...$this->generateRandomIntArray());
		$noiseClouds = new Simplex(...$this->generateRandomIntArray());

		$size = self::RESOLUTION;
		$gridX = $this->fgs[0] + ($size / 2);
		$gridY = $this->fgs[1] + ($size / 2);

		$ugsImage = imagecreatefrompng($this->dirPath . "/../tc-ugs.png");
		$fgsColour = imagecolorsforindex($ugsImage, imagecolorat($ugsImage, $gridX, $gridY));

		$data = [
			"brightness" => [],
			"r" => $fgsColour["red"] / 255,
			"g" => $fgsColour["green"] / 255,
			"b" => $fgsColour["blue"] / 255,
			"filaments" => [],
		];

		$totalBrightness = 0;

		for($y = 0; $y < $size; $y++) {
			$rowBrightness = [];
			$rowFilament = [];

			for($x = 0; $x < $size; $x++) {
				$mappedX = ($x / $size) + $gridX;
				$mappedY = ($y / $size) + $gridY;
				$brightness = round($noiseBrightness->valueAt($mappedX, $mappedY), 3);
				$brightness += ($data["r"] + $data["g"] + $data["b"]) / 10;
				$brightness = min(1.0, $brightness);
				array_push($rowBrightness, $brightness);
				$totalBrightness += $brightness;

				$filament = round($noiseClouds->valueAt($mappedX * 6, $mappedY * 6), 3);
				array_push($rowFilament, $filament);
			}

			array_push($data["brightness"], $rowBrightness);
			array_push($data["filaments"], $rowFilament);
		}

		// Star count in Milky Way galaxy is 400,000,000,000
		// FGS cells in each UGS cell is 128x128 (16,384)
		// So, average star count per FGS cell is 400,000,000,000 / 16,384
		// = 24,414,062.5
		for($i = 0; $i < $totalBrightness; $i ++) {
			$x = $this->rand->getInt(0, $size - 1);
			$y = $this->rand->getInt(0, $size - 1);
			$brightness = $this->rand->getInt(0, 2);

			if($i > ($totalBrightness * 0.8)) {
				$brightness *= 4;
			}

			if($brightness > 0) {
				$data["brightness"][$y][$x] += $brightness;
			}
		}

		return $data;
	}

	protected function cache():void {
		$brightnessData = $this->data["brightness"];

		$image = imagecreatetruecolor(
			count($brightnessData),
			count($brightnessData[0])
		);

		$brightestValue = 0;
		array_walk_recursive(
			$brightnessData,
			function($brightness)use(&$brightestValue) {
				if($brightness > $brightestValue) {
					$brightestValue = $brightness;
				}
			}
		);

		$reductionFactor = 1;
		foreach($brightnessData as $y => $rowBrightness) {
			foreach($rowBrightness as $x => $brightness) {
				$filamentScale = (($this->data["filaments"][$y][$x] + 1) / 4);
				$scale = ($brightness + 1) / 2;
				$scale *= $reductionFactor;
				$r = $this->data["r"] * $scale;
				$g = $this->data["g"] * $scale;
				$b = $this->data["b"] * $scale;

				$r += $filamentScale;
				$g += $filamentScale;
				$b += $filamentScale;

				$r = floor($r * 255);
				$r = min($r, 255);
				$r = max(0, $r);
				$g = floor($g * 255);
				$g = min($g, 255);
				$g = max(0, $g);
				$b = floor($b * 255);
				$b = min($b, 255);
				$b = max(0, $b);

				$r = floor(min($r, 255));
				$g = floor(min($g, 255));
				$b = floor(min($b, 255));

				$c = imagecolorclosest($image, $r, $g, $b);
				imagesetpixel($image, $x, $y, $c);
			}
		}

		imagepng($image, "$this->dirPath/tc-fgs.png");
	}
}
