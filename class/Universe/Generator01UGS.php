<?php
namespace Space\Universe;

use Space\Noise\Simplex;

/**
 * Universal Grid Sector.
 * 0, 0 is the centre of universal observation, relative to the viewer. The
 * grid extends infinitely in either direction of the X and Y axis.
 *
 * Each cell is ~ 130.5 million light years square.
 */
class Generator01UGS extends AbstractUniverseGenerator {
	public const CODE = "ugs";
	public const REGEX = AbstractUniverseGenerator::REGEX
		. self::CODE
		. "=(?P<UGS_X>[+-]\d+):(?P<UGS_Y>[+-]\d+)";
	public const MIN = -INF;
	public const MAX = +INF;

	protected function generate():array {
		$data = [
			"brightness" => [],
			"r" => [],
			"g" => [],
			"b" => [],
		];

		$noiseBrightness = new Simplex(...$this->generateRandomIntArray());
		$noiseR = new Simplex(...$this->generateRandomIntArray());
		$noiseG = new Simplex(...$this->generateRandomIntArray());
		$noiseB = new Simplex(...$this->generateRandomIntArray());

		$size = Generator02LGS::MAX - Generator02LGS::MIN;

		$gridX = $this->ugs[0];
		$gridY = $this->ugs[1];

		for($y = 0; $y < $size; $y++) {
			$rowBrightness = [];
			$rowR = [];
			$rowG = [];
			$rowB = [];

			for($x = 0; $x < $size; $x++) {
				$mappedX = ($x / $size) + $gridX;
				$mappedY = ($y / $size) + $gridY;

				$brightness = round($noiseBrightness->valueAt($mappedX, $mappedY), 3);
				$r = round($noiseR->valueAt($mappedX, $mappedY), 3);
				$g = round($noiseG->valueAt($mappedX, $mappedY), 3);
				$b = round($noiseB->valueAt($mappedX, $mappedY), 3);

				array_push($rowBrightness, $brightness);
				array_push($rowR, $r);
				array_push($rowG, $g);
				array_push($rowB, $b);
			}

			array_push($data["brightness"], $rowBrightness);
			array_push($data["r"], $rowR);
			array_push($data["g"], $rowG);
			array_push($data["b"], $rowB);
		}

		return $data;
	}

	protected function cache():void {
		$image = imagecreatetruecolor(
			count($this->data["brightness"]),
			count($this->data["brightness"][0])
		);

		foreach($this->data["brightness"] as $y => $rowBrightness) {
			foreach($rowBrightness as $x => $brightness) {
				$scale = ($brightness + 1) / 2;

				$r = pow(($this->data["r"][$y][$x] + 1) / 2, 3);
				$g = pow(($this->data["g"][$y][$x] + 1) / 2, 3);
				$b = pow(($this->data["b"][$y][$x] + 1) / 2, 3);
				$ra = floor(($r * $g * $b) * $r * $scale * 10000);
				$ga = floor(($r * $g * $b) * $g * $scale * 10000);
				$ba = floor(($r * $g * $b) * $b * $scale * 10000);
				$c = imagecolorclosest($image, min($ra, 255), min($ga, 255), min($ba, 255));
				imagesetpixel($image, $x, $y, $c);
			}
		}

		imagepng($image, "$this->dirPath/cMap.png");
	}

	/** @return array<int> */
	private function generateRandomIntArray():array {
		$intArray = [];
		for($i = 0; $i < 256; $i ++) {
			array_push($intArray, $this->rand->getInt(0, 255));
		}
		return $intArray;
	}
}
