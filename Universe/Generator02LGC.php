<?php
namespace Space\Universe;

class Generator02LGC extends AbstractUniverseGenerator {
	public const REGEX = Generator01USC::REGEX . " lgc=(?P<LGC_X>[pn]\d+):(?P<LGC_Y>[pn]\d+)";
}
