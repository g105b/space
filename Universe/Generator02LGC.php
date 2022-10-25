<?php
namespace Space\Universe;

class Generator02LGC extends AbstractUniverseGenerator {
	public const REGEX = Generator01USC::REGEX . " lgc=(?P<LGC_X>[pn]\d+):(?P<LGC_Y>[pn]\d+)";

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
		$brightnessFactor = $this->prevData[$this->lgc[1]][$this->lgc[0]];
		$data = [];

		for($y = -30; $y < 30; $y++) {
			for($x = -30; $x < 30; $x++) {
				$scalar = $this->rand->getScalar();
				$scalar *= $this->rand->getInt(0, $brightnessFactor);
				$scalar /= 5;
				$brightness = 0;
				if($scalar > 0.199) {
					$brightness = 1;
				}
				if($scalar > 0.399) {
					$brightness = 2;
				}
				if($scalar > 0.55) {
					$brightness = 3;
				}
				if($scalar > 0.759) {
					$brightness = 4;
				}
				if($scalar > 0.99) {
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
