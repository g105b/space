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

		$size = self::RESOLUTION;

		$gridX = $this->ugs[0];
		$gridY = $this->ugs[1];

		$starCount = 0;
		$starThreshold = 1.75;

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
				if($brightness + $r + $g + $b > $starThreshold) {
					$starCount++;
				}

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

		// Generate the visible stars:
		for($i = 0; $i < $starCount * 10; $i++) {
			$x = $this->rand->getInt(0, $size - 1);
			$y = $this->rand->getInt(0, $size - 1);
			$dist = $this->rand->getInt(0, $size / 4);
			$angle = ($this->rand->getInt(0, 3599) / 1000) * (pi() / 180);
			$multiplier = ($this->rand->getInt(0, 500) / 1000) + 1;
			$combined = array_sum([
				$data["brightness"][$y][$x],
				$data["r"][$y][$x],
				$data["g"][$y][$x],
				$data["b"][$y][$x],
			]);
			if($combined > $starThreshold) {
				$newX = floor($x + ($dist * cos($angle)));
				$newY = floor($y + ($dist * sin($angle)));
				if($newX < 0 || $newX >= $size || $newY < 0 || $newY >= $size) {
					continue;
				}

				$data["brightness"][$newY][$newX] *= $multiplier;
				$data["r"][$newY][$newX] *= $multiplier;
				$data["g"][$newY][$newX] *= $multiplier;
				$data["b"][$newY][$newX] *= $multiplier;
			}
		}

		array_walk_recursive($data, function(float &$value) {
			$value = min($value, 1);
			$value = max(-1, $value);
		});

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

		imagepng($image, "$this->dirPath/tc-ugs.png");
	}
}
