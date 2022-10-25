<?php
namespace Space\Universe;

use g105b\drng\Random;
use g105b\drng\StringSeed;
use OutOfBoundsException;

abstract class AbstractUniverseGenerator {
	public const REGEX = "(?P<MULTIVERSE_ID>[^@]+)@";

	protected Random $rand;

	protected string $multiverseId;
	/** @var array<int, int> */
	protected array $usc;
	/** @var array<int, int> */
	protected array $lgc;
	/** @var array<int, int> */
	protected array $ggc;
	/** @var array<int, int> */
	protected array $sgc;
	/** @var array<float, float> */
	protected array $ssc;

	protected array $data;

	public function __construct(
		protected string $locator,
		protected ?array $prevData = null,
	) {
		$this->rand = new Random(
			new StringSeed($locator)
		);
		$this->extractLocatorParts($locator);
		$this->data = $this->generate();
	}

	abstract public function __toString():string;

	abstract protected function generate():array;

	public function getData():array {
		return $this->data;
	}

	protected function extractLocatorParts(string $locator):void {
		if(!preg_match("/" . static::REGEX . "/", $locator, $matches)) {
			throw new InvalidLocatorException(
				"Required syntax: " . static::REGEX . " (received: $locator)"
			);
		}

		$this->multiverseId = $matches["MULTIVERSE_ID"];
		if(isset($matches["USC_X"]) && isset($matches["USC_Y"])) {
			$this->usc = $this->extractCoords("USC", $matches);
		}

		if(isset($matches["LGC_X"]) && isset($matches["LGC_Y"])) {
			$this->lgc = $this->extractCoords("LGC", $matches);
			$this->clamp("LGC", $this->lgc, -100, 100);
		}

		if(isset($matches["GGC_X"]) && isset($matches["GGC_Y"])) {
			$this->ggc = $this->extractCoords("GGC", $matches);
			$this->clamp("GGC", $this->ggc, -30, 30);
		}

		if(isset($matches["SGC_X"]) && isset($matches["SGC_Y"])) {
			$this->sgc = $this->extractCoords("SGC", $matches);
			$this->clamp("SGC", $this->sgc, -100, 100);
		}

		if(isset($matches["SSC_Z"]) && isset($matches["SSC_LON"])) {
			$this->ssc = $this->extractPosition("SSC", $matches);
			$this->clampPosition("SSC", $this->ssc, 1031324);
		}
	}

	private function extractCoords(string $type, array $matches):array {
		$x = $matches["{$type}_X"];
		$xScale = $x[0] === "p" ? 1 : -1;
		$x = ((int)substr($x, 1)) * $xScale;

		$y = $matches["{$type}_Y"];
		$yScale = $y[0] === "p" ? 1 : -1;
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
