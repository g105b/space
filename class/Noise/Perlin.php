<?php
namespace Space\Noise;

use RuntimeException;

class Perlin {
	/** @var array<float> */
	private array $seed;
	/** @var array<float> */
	private array $buffer;

	/**
	 * @param float<0, 1> $brightness
	 * @param float<0, 1> $contrast
	 */
	public function __construct(
		public readonly int $width = 256,
		public readonly int $height = 256,
		public int $octaves = 8,
		public float $scalingBias = 1.6,
		public float $brightness = 1 ,
		public float $contrast = 1,
		float...$seed
	) {
		if(empty($seed)) {
			$seed = $this->randomSeed($width, $height);
		}

		$this->seed = $seed;
		$this->buffer = [];
	}



	/** return array<float> */
	public function randomSeed(int $width, int $height):array {
		$array = [];

		for($i = 0; $i < $width * $height; $i++) {
			array_push($array, rand(0, 256) / 256);
		}

		return $array;
	}

	public function generate():void {
		for($x = 0; $x < $this->width; $x++) {
			for($y = 0; $y < $this->height; $y++) {
				$this->generateXY($x, $y);
			}
		}
	}

	public function writeToFile(string $fileName = "out.png"):void {
		$this->checkGenerated();

		$image = imagecreatetruecolor($this->width, $this->height);
		$colorRange = [];
		for($i = 0; $i < 256; $i++) {
			array_push(
				$colorRange,
				imagecolorallocate($image, $i, $i, $i)
			);
		}

		for($x = 0; $x < $this->width; $x++) {
			for($y = 0; $y < $this->height; $y++) {
				$index = $y * $this->width + $x;
				$pixelValue = $this->buffer[$index] * 255.0;

				$pixelValueFromCentre = abs(127 - $pixelValue);
				$pixelValueExaggeration = $this->contrast * $pixelValueFromCentre;
				if($pixelValue < 127) {
					$pixelValue = 127 - $pixelValueExaggeration;
				}
				else {
					$pixelValue = 127 + $pixelValueExaggeration;
				}

				$pixelValue += (($this->brightness - 1) * 255);

				$pixelValue = min(255, $pixelValue);
				$pixelValue = max(0, $pixelValue);
				$pixelValue = round($pixelValue);
				imagesetpixel($image, $x, $y, $colorRange[$pixelValue]);
			}
		}

		imagepng($image, $fileName);
	}

	private function checkGenerated():void {
		if(!empty($this->buffer)) {
			return;
		}

		throw new RuntimeException("Perlin noise has not yet been generated");
	}

	private function generateXY(int $x, int $y):void {
		$noiseScale = 0;
		$accumulatedScale = 0;
		$scale = 1;

		for($o = 0; $o < $this->octaves; $o++) {
			$pitch = $this->width >> $o;
			$sampleX1 = floor($x / $pitch) * $pitch;
			$sampleY1 = floor($y / $pitch) * $pitch;
			$sampleX2 = round($sampleX1 + $pitch) % $this->width;
			$sampleY2 = round($sampleY1 + $pitch) % $this->height;

			$blendX = ($x - $sampleX1) / $pitch;
			$blendY = ($y - $sampleY1) / $pitch;

			$sampleT = (1.0 - $blendX)
				* $this->seed[$sampleY1 * $this->width + $sampleX1]
				+ $blendX
				* $this->seed[$sampleY1 * $this->width + $sampleX2];
			$sampleB = (1.0 - $blendX)
				* $this->seed[$sampleY2 * $this->width + $sampleX1]
				+ $blendX
				* $this->seed[$sampleY2 * $this->width + $sampleX2];

			$accumulatedScale += $scale;
			$noiseScale += ($blendY * ($sampleB - $sampleT) + $sampleT) * $scale;
			$scale = $scale / $this->scalingBias;
		}

		$this->buffer[$y * $this->width + $x] = $noiseScale / $accumulatedScale;
	}
}
