<?php
namespace Space\Universe;

/**
 * Universal Sector Coordinates.
 * 0, 0 is the centre of universal observation, relative to the viewer. The
 * grid extends infinitely in either direction of the X and Y axis.
 *
 * Each cell is ~ 40 million lightyears square.
 */
class Generator01USC extends AbstractUniverseGenerator {
	public const REGEX = AbstractUniverseGenerator::REGEX . "usc=(?P<USC_X>[pn]\d+):(?P<USC_Y>[pn]\d+)";

	public function __toString():string {
		$string = "";
		foreach($this->data as $y => $row) {
			foreach($row as $x => $brightness) {
				$string .= match($brightness) {
					0 => " ",
					1 => "·",
					2 => "○",
					3 => "●",
					4 => "⦿",
					5 => "🌑",
				};
			}
			$string .= PHP_EOL;
		}

		return $string;
	}

	protected function generate():array {
		$data = [];
		for($y = -100; $y < 100; $y++) {
			$data[$y] = [];

			for($x = -100; $x < 100; $x++) {
				$scalar = $this->rand->getScalar();
				$brightness = 0;
				if($scalar > 0.9) {
					$brightness = 1;
				}
				if($scalar > 0.97) {
					$brightness = 2;
				}
				if($scalar > 0.98) {
					$brightness = 3;
				}
				if($scalar > 0.99) {
					$brightness = 4;
				}
				if($scalar > 0.999) {
					$brightness = 5;
				}

				if($x === 0 && $y === 0) {
					$brightness = 2;
				}

				$data[$y][$x] = $brightness;
			}
		}
		return $data;
	}
}
