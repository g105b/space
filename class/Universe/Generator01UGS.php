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
		];

		$noise = new Simplex(...$this->generateRandomIntArray());

		for($y = Generator02LGS::MIN; $y < Generator02LGS::MAX; $y++) {
			$row = [];

			for($x = Generator02LGS::MIN; $x < Generator02LGS::MAX; $x++) {
				$mappedX = $this->map(
					$x,
					Generator02LGS::MIN,
					Generator02LGS::MAX,
					0,
					1,
				);
				$mappedY = $this->map(
					$y,
					Generator02LGS::MIN,
					Generator02LGS::MAX,
					0,
					1,
				);
				$value = $noise->valueAt($mappedX, $mappedY);
				array_push($row, $value);
			}

			array_push($data["brightness"], $row);
		}

		return $data;
	}

	protected function cache():void {
		$brightnessData = $this->data["brightness"];

		$image = imagecreatetruecolor(count($brightnessData[0]), count($brightnessData));
		$c = [];
		for($i = 0; $i < 256; $i++) {
			array_push($c, imagecolorallocate($image, $i, $i, $i));
		}
		foreach($brightnessData as $y => $row) {
			foreach($row as $x => $value) {
				$cIndex = (int)floor(($value + 1) * 127);
				imagesetpixel($image, $x, $y, $c[$cIndex]);
			}
		}

		$coords = "ugs_";
		if($this->ugs[0] >= 0) {
			$coords .= "+";
		}
		$coords .= $this->ugs[0];
		$coords .= ":";
		if($this->ugs[1] >= 0) {
			$coords .= "+";
		}
		$coords .= $this->ugs[1];

// TODO: Generate the different colour maps here, storing individually in the dirPath, then combining for the overall image in the parent directory, named by the $coords.

		imagepng($image, "$this->dirPath/brightness.png");
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
