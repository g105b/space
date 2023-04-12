<?php
namespace Space\Universe;

use g105b\drng\Random;
use g105b\drng\StringSeed;
use OutOfBoundsException;

abstract class AbstractUniverseGenerator {
	public const CODE = "xyz";
	public const REGEX = "(?P<MULTIVERSE_ID>[^@]+)@";
	public const MIN = 0;
	public const MAX = 0;

	protected Random $rand;

	public readonly string $multiverseId;
	/** @var array<int, int> */
	public readonly array $ugs;
	/** @var array<int, int> */
	public readonly array $lgs;
	/** @var array<int, int> */
	public readonly array $ggs;
	/** @var array<int, int> */
	public readonly array $sgs;
	/** @var array<float, float> */
	public readonly array $ssc;

	public readonly string $dirPath;
	public readonly array $data;

	public function __construct(
		public readonly string $locator,
		protected readonly ?array $prevData = null,
	) {
		$this->rand = new Random(
			new StringSeed($locator)
		);
		$this->extractLocatorParts($locator);
		$this->dirPath = $this->getDirPath();
		if(!is_dir($this->dirPath)) {
			mkdir($this->dirPath, recursive: true);
		}

		$this->data = $this->generate();
		$this->cache();
	}

	abstract protected function generate():array;

	abstract protected function cache():void;

	/**
	 * @param float $value the incoming value to be converted
	 * @param float $lowCurrent lower bound of the value's current range
	 * @param float $highCurrent upper bound of the value's current range
	 * @param float $lowTarget lower bound of the value's target range
	 * @param float $highTarget upper bound of the value's target range
	 * @return float
	 */
	protected function map(
		float $value,
		float $lowCurrent,
		float $highCurrent,
		float $lowTarget,
		float $highTarget,
	):float {
		return ($value / ($highCurrent - $lowCurrent))
			* ($highTarget - $lowTarget) + $lowTarget;
	}

	protected function extractLocatorParts(string $locator):void {
		if(!preg_match("/" . static::REGEX . "/", $locator, $matches)) {
			throw new InvalidLocatorException(
				"Required syntax: " . static::REGEX . " (received: $locator)"
			);
		}

		$this->multiverseId = $matches["MULTIVERSE_ID"];
		if(isset($matches["UGS_X"]) && isset($matches["UGS_Y"])) {
			$this->ugs = $this->extractCoords("UGS", $matches);
		}

		if(isset($matches["LGS_X"]) && isset($matches["LGS_Y"])) {
			$this->lgs = $this->extractCoords("LGS", $matches);
			$this->clamp("LGS", $this->lgs, -100, 100);
		}

		if(isset($matches["GGS_X"]) && isset($matches["GGS_Y"])) {
			$this->ggs = $this->extractCoords("GGS", $matches);
			$this->clamp("GGS", $this->ggs, -30, 30);
		}

		if(isset($matches["SGS_X"]) && isset($matches["SGS_Y"])) {
			$this->sgs = $this->extractCoords("SGS", $matches);
			$this->clamp("SGS", $this->sgs, -100, 100);
		}

		if(isset($matches["SPS_Z"]) && isset($matches["SSC_LON"])) {
			$this->ssc = $this->extractPosition("SSC", $matches);
			$this->clampPosition("SSC", $this->ssc, 1031324);
		}
	}

	private function getDirPath():string {
		$dirPath = "data/universe/$this->multiverseId";

		if(isset($this->ugs)) {
			$dirPath .="/ugs_";
			$dirPath .= ($this->ugs[0] >= 0) ? "+" : "";
			$dirPath .= $this->ugs[0];
			$dirPath .= ":";
			$dirPath .= ($this->ugs[1] >= 0) ? "+" : "";
			$dirPath .= $this->ugs[1];
		}

		return $dirPath;
	}

	private function extractCoords(string $type, array $matches):array {
		$x = $matches["{$type}_X"];
		$xScale = $x[0] === "+" ? 1 : -1;
		$x = ((int)substr($x, 1)) * $xScale;

		$y = $matches["{$type}_Y"];
		$yScale = $y[0] === "+" ? 1 : -1;
		$y = ((int)substr($y, 1)) * $yScale;

		return[$x, $y];
	}

	private function extractPosition(string $type, array $matches):array {
		$z = $matches["{$type}_Z"];
		$zScale = $z[0] === "p" ? 1 : -1;
		$z = ((float)substr($z, 1)) * $zScale;
		$lon = $matches["{$type}_LON"];
		$lonScale = $lon[0] === "p" ? 1 : -1;
		$lon = ((float)substr($lon, 1)) * $lonScale;

		if($lat = $matches["{$type}_LAT"] ?? null) {
			$latScale = $lat[0] === "p" ? 1 : -1;
			$lat = ((float)substr($lat, 1)) * $latScale;
		}

		return array_filter([
			$z,
			$lon,
			$lat,
		]);
	}

	private function clamp(string $type, array $coords, int $min, int $max):void {
		foreach($coords as $c) {
			if($c < $min || $c > $max) {
				throw new OutOfBoundsException("$type coords are out of bounds (min $min, max $max, given $c)");
			}
		}
	}

	private function clampPosition(string $type, array $position, int $maxDist, int $minDist = 0):void {
		foreach($position as $i => $p) {
			if($i === 0) {
				if($p < $minDist) {
					throw new OutOfBoundsException("$type position distance is too low (min $minDist, given $p)");
				}
				if($p > $maxDist) {
					throw new OutOfBoundsException("$type position distance is too high (max $maxDist, given $p)");
				}
			}
			else {
				if($p < -180 || $p > 180) {
					throw new OutOfBoundsException("$type position angle is out of bounds (given $p)");
				}
			}
		}
	}
}
