<?php
namespace Space\Noise;

class Simplex {
	private array $pointsMod12 = [];
	private array $points = [];
	private float $F2;
	private float $G2;
	/** @var array<int> */
	private array $seed;

	const GRADIENT_MAP = [
		[1.0, 1.0, 0.0],
		[-1.0, 1.0, 0.0],
		[1.0, -1.0, 0.0],
		[-1.0, -1.0, 0.0],
		[1.0, 0.0, 1.0],
		[-1.0, 0.0, 1.0],
		[1.0, 0.0, -1.0],
		[-1.0, 0.0, -1.0],
		[0.0, 1.0, 1.0],
		[0.0, -1.0, 1.0],
		[0.0, 1.0, -1.0],
		[0.0, -1.0, -1.0]
	];

	/** @param array<int> $seed */
	public function __construct(
		int...$seed,
	) {
		if(empty($seed)) {
			$this->seed = [];
			for($i = 0; $i < 256; $i++) {
				array_push($this->seed, rand(0, 255));
			}
		}
		elseif(count($seed) !== 256) {
			throw new NoiseException("Seed must be 256 items");
		}
		else {
			$this->seed = $seed;
		}


		for($i = 0; $i < 512; $i++) {
			$value = $this->seed[$i & 255];
			$this->points[$i] = $value;
			$this->pointsMod12[$i] = $value % 12;
		}

		$this->F2 = 0.5 * (sqrt(3.0) - 1.0);
		$this->G2 = (3.0 - sqrt(3.0)) / 6.0;
	}

	public function valueAt(float $x, float $y):float {
		$s = ($x + $y) * $this->F2;

		$i = $this->fastFloor($x + $s);
		$j = $this->fastFloor($y + $s);

		$t = ($i + $j) * $this->G2;

		$x00 = $i - $t;
		$y00 = $j - $t;

		$x0 = $x - $x00;
		$y0 = $y - $y00;

		$i1 = 0;
		$j1 = 1;
		if($x0 > $y0) {
			$i1 = 1;
			$j1 = 0;
		}

		$x1 = $x0 - $i1 + $this->G2;
		$y1 = $y0 - $j1 + $this->G2;

		$x2 = $x0 - 1.0 + 2.0 * $this->G2;
		$y2 = $y0 - 1.0 + 2.0 * $this->G2;

		$ii = $i & 255;
		$jj = $j & 255;

		$index0 = $ii + $this->points[$jj];
		$index1 = $ii + $i1 + $this->points[$jj + $j1];
		$index2 = $ii + 1 + $this->points[$jj + 1];

		$gi0 = (int)$this->pointsMod12[$index0];
		$gi1 = (int)$this->pointsMod12[$index1];
		$gi2 = (int)$this->pointsMod12[$index2];

		$n0 = $this->getN($x0, $y0, self::GRADIENT_MAP[$gi0]);
		$n1 = $this->getN($x1, $y1, self::GRADIENT_MAP[$gi1]);
		$n2 = $this->getN($x2, $y2, self::GRADIENT_MAP[$gi2]);

		return 70.0 * ($n0 + $n1 + $n2);
	}

	private function fastFloor(float $value):int {
		$intValue = (int)$value;
		return $value < $intValue ? $intValue - 1 : $intValue;
	}

	private function dot(array $points, float $x, float $y):float {
		return $points[0] * $x + $points[1] * $y;
	}

	private function getN(float $x, float $y, array $gradient):float {
		$t = (0.5 - $x * $x - $y * $y);
		if($t < 0) {
			return 0.0;
		}
		$t *= $t;
		return $t * $t * $this->dot($gradient, $x, $y);
	}
}
