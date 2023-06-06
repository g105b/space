<?php
namespace Space\Universe;

use Space\Noise\Simplex;

class Generator03GGS extends AbstractUniverseGenerator {
	public const CODE = "ggs";
	public const REGEX = Generator02FGS::REGEX . " ggs=(?P<GGS_X>[+-]\d+):(?P<GGS_Y>[+-]\d+)";
	public const MIN = -100;
	public const MAX = +100;

	protected function generate():array {
		$noiseBrightness = new Simplex(...$this->generateRandomIntArray());
		$noiseStarDensity = new Simplex(...$this->generateRandomIntArray());

		$size = Generator04SGS::MAX - Generator04SGS::MIN;
		$fgsGridX = $this->fgs[0] + ($size / 2);
		$fgsGridY = $this->fgs[1] + ($size / 2);
		$gridX = $this->ggs[0] + ($size / 2);
		$gridY = $this->ggs[1] + ($size / 2);

		$ugsImage = imagecreatefrompng($this->dirPath . "/../cMap.png");
		$fgsColour = imagecolorsforindex($ugsImage, imagecolorat($ugsImage, $fgsGridX, $fgsGridY));

		$data = [
			"brightness" => [],
			"r" => $fgsColour["red"] / 255,
			"g" => $fgsColour["green"] / 255,
			"b" => $fgsColour["blue"] / 255,
			"stars" => [],
		];

		$totalBrightness = 0;

		for($y = 0; $y < $size; $y++) {
			$rowBrightness = [];

			for($x = 0; $x < $size; $x++) {
				/** @noinspection DuplicatedCode This is intentionally similar to FGS generation */
				$mappedX = (($x / $size) + $gridX);
				$mappedY = (($y / $size) + $gridY);
				$brightness = round($noiseBrightness->valueAt($mappedX, $mappedY), 3);
				$brightness += ($data["r"] + $data["g"] + $data["b"]) / 10;
				$brightness = min(1.0, $brightness);
				array_push($rowBrightness, $brightness);
				$totalBrightness += $brightness;
			}

			array_push($data["brightness"], $rowBrightness);
		}

		for($i = 0; $i < $totalBrightness; $i++) {
			$x = $this->rand->getInt(0, $size - 1);
			$y = $this->rand->getInt(0, $size - 1);

			$mappedX = ($x / $size) + $gridX;
			$mappedY = ($y / $size) + $gridY;

			$brightness = $noiseStarDensity->valueAt($mappedX * 10, $mappedY * 10);
			$data["stars"][$y][$x] = $brightness;
		}

		foreach(array_keys($data["stars"]) as $y) {
			ksort($data["stars"][$y]);
		}
		ksort($data["stars"]);

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
				$scale = ($brightness + 1) / 2;
				$scale *= $reductionFactor;
				$r = $this->data["r"] * $scale;
				$g = $this->data["g"] * $scale;
				$b = $this->data["b"] * $scale;

				$r = floor($r * 255);
				$r = min($r, 255);
				$r = max(0, $r);
				$g = floor($g * 255);
				$g = min($g, 255);
				$g = max(0, $g);
				$b = floor($b * 255);
				$b = min($b, 255);
				$b = max(0, $b);

				if(isset($this->data["stars"][$y][$x])) {
					$starValue = $this->data["stars"][$y][$x];
					$starValue = (($starValue + 1) / 2) * 255;
					$r += $starValue;
					$g += $starValue;
					$b += $starValue;
				}

				$r = floor(min($r, 255));
				$g = floor(min($g, 255));
				$b = floor(min($b, 255));

				$c = imagecolorclosest($image, $r, $g, $b);
				imagesetpixel($image, $x, $y, $c);
			}
		}

		imagepng($image, "$this->dirPath/cMap.png");
	}
}
