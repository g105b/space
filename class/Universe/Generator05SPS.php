<?php
namespace Space\Universe;

class Generator05SPS extends AbstractUniverseGenerator {
// TODO: in the AbstractUniverseGenerator::extractPosition, I'm not handling TURNs yet.
	public const REGEX = Generator04SGS::REGEX . " ssc=(?P<SPS_Z>[\d.]+):(?P<SSC_TURN>[\d.]+)";

	protected function generate():array {
		// TODO: Implement generate() method.
	}

	protected function cache():void {
		// TODO: Implement cache() method.
	}
}
