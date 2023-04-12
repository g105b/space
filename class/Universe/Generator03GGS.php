<?php
namespace Space\Universe;

class Generator03GGS extends AbstractUniverseGenerator {
	public const REGEX = Generator02LGS::REGEX . " ggs=(?P<GGS_X>[+-]\d+):(?P<GGS_Y>[+-]\d+)";
	public const MIN = -30;
	public const MAX = +30;

	public function __toString():string {
		// TODO: Implement __toString() method.
	}

	protected function generate():array {
		// TODO: Implement generate() method.
	}

	protected function cache():void {
		// TODO: Implement cache() method.
	}
}
