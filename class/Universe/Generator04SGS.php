<?php
namespace Space\Universe;

class Generator04SGS extends AbstractUniverseGenerator {
	public const REGEX = Generator03GGS::REGEX . " sgc=(?P<SGS_X>[+-]\d+):(?P<SGS_Y>[+-]\d+)";

	public const MIN = -100;
	public const MAX = +100;

	protected function generate():array {
		// TODO: Implement generate() method.
	}

	protected function cache():void {
		// TODO: Implement cache() method.
	}
}
