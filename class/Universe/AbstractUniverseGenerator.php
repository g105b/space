<?php
namespace Space\Universe;

use g105b\drng\Random;
use g105b\drng\StringSeed;
use OutOfBoundsException;

abstract class AbstractUniverseGenerator {
	public const CODE = "xyz";
	public const REGEX = "(?P<UNIVERSE_ID>[^@]+)@";
	public const MIN = 0;
	public const MAX = 0;

	protected Random $rand;

	public readonly string $universeId;
	/** @var array<int, int> */
	public readonly array $ugs;
	/** @var array<int, int> */
	public readonly array $fgs;
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
		bool $useCache = true,
	) {
		$this->rand = new Random(
			new StringSeed($this->getUniverse($locator))
		);
		$this->extractLocatorParts($locator);
		$this->dirPath = $this->getDirPath();
		if(!is_dir($this->dirPath)) {
			mkdir($this->dirPath, recursive: true);
		}

		if(!$useCache || empty(glob($this->dirPath . "/*.*"))) {
			$this->data = $this->generate();
			$this->cache();
		}
	}

	abstract protected function generate():array;

	abstract protected function cache():void;

	/**
	 * @param float $n the incoming value to be converted
	 * @param float $start1 lower bound of the value's current range
	 * @param float $stop1 upper bound of the value's current range
	 * @param float $start2 lower bound of the value's target range
	 * @param float $stop2 upper bound of the value's target range
	 * @return float
	 */
	protected function map(
		float $n, //$value
		float $start1, //$lowCurrent
		float $stop1, //$highCurrent
		float $start2, //$lowTarget
		float $stop2, //$highTarget
		bool $withinBounds = true,
	):float {
		$newval = ($n - $start1) / ($stop1 - $start1) * ($stop2 - $start2) + $start2;
		if(!$withinBounds) {
			return $newval;
		}
		if ($start2 < $stop2) {
			return $this->constrain($newval, $start2, $stop2);
		} else {
			return $this->constrain($newval, $stop2, $start2);
		}
	}

	private function constrain(float $n, float $low, float $high) {
		return max(min($n, $high), $low);
	}

	protected function extractLocatorParts(string $locator):void {
		if(!preg_match("/" . static::REGEX . "/", $locator, $matches)) {
			throw new InvalidLocatorException(
				"Required syntax: " . static::REGEX . " (received: $locator)"
			);
		}

		$this->universeId = $matches["UNIVERSE_ID"];
		if(isset($matches["UGS_X"]) && isset($matches["UGS_Y"])) {
			$this->ugs = $this->extractCoords("UGS", $matches);
		}

		if(isset($matches["FGS_X"]) && isset($matches["FGS_Y"])) {
			$this->fgs = $this->extractCoords("FGS", $matches);
			$this->clamp("FGS", $this->fgs, -100, 100);
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

	/** @return array<int> */
	protected function generateRandomIntArray(int $length = 256):array {
		$intArray = [];
		for($i = 0; $i < $length; $i ++) {
			array_push($intArray, $this->rand->getInt(0, 255));
		}
		return $intArray;
	}

	private function getDirPath():string {
		$dirPath = "data/universe/$this->universeId";

		if(isset($this->ugs)) {
			$dirPath .="/ugs_";
			$dirPath .= ($this->ugs[0] >= 0) ? "+" : "";
			$dirPath .= $this->ugs[0];
			$dirPath .= ":";
			$dirPath .= ($this->ugs[1] >= 0) ? "+" : "";
			$dirPath .= $this->ugs[1];
		}

		if(isset($this->fgs)) {
			$dirPath .= "/fgs_";
			$dirPath .= ($this->fgs[0] >= 0) ? "+" : "";
			$dirPath .= $this->fgs[0];
			$dirPath .= ":";
			$dirPath .= ($this->fgs[1] >= 0) ? "+" : "";
			$dirPath .= $this->fgs[1];
		}

		if(isset($this->ggs)) {
			$dirPath .= "/ggs_";
			$dirPath .= ($this->ggs[0] >= 0) ? "+" : "";
			$dirPath .= $this->ggs[0];
			$dirPath .= ":";
			$dirPath .= ($this->ggs[1] >= 0) ? "+" : "";
			$dirPath .= $this->ggs[1];
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

	private function getUniverse(string $locator):string {
		return strtok($locator, "@");
	}
}
