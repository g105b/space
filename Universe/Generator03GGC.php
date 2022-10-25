<?php
namespace Space\Universe;

class Generator03GGC extends AbstractUniverseGenerator {
	public const REGEX = Generator02LGC::REGEX . " ggc=(?P<GGC_X>[pn]\d+):(?P<GGC_Y>[pn]\d+)";

	public function __toString():string {
		// TODO: Implement __toString() method.
	}

	protected function generate():array {
		// TODO: Implement generate() method.
	}
}
