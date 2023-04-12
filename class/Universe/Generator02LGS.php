<?php
namespace Space\Universe;

class Generator02LGS extends AbstractUniverseGenerator {
	public const CODE = "lgc";
	public const REGEX = Generator01UGS::REGEX . " lgs=(?P<LGS_X>[+-]\d+):(?P<LGS_Y>[+-]\d+)";
	public const MIN = -100;
	public const MAX = +100;

	protected function generate():array {
		$brightnessFactor = $this->prevData[$this->lgs[1]][$this->lgs[0]];
		$data = [];

		for($y = Generator03GGS::MIN; $y < Generator03GGS::MAX; $y++) {
			for($x = Generator03GGS::MIN; $x < Generator03GGS::MAX; $x++) {
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

				if($this->ugs[0] === 0 && $this->ugs[1] === 0 && $this->lgs[0] === 0 && $this->lgs[1] === 0 && $x === 0 && $y === 0) {
					$brightness = 2;
				}

				$data[$y][$x] = $brightness;
			}
		}

		return $data;
	}

	protected function cache():void {
		// TODO: Implement cache() method.
	}
}
