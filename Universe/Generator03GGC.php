<?php
namespace Space\Universe;

class Generator03GGC extends AbstractUniverseGenerator {
	public const REGEX = Generator02LGC::REGEX . " ggc=(?P<GGC_X>[pn]\d+):(?P<GGC_Y>[pn]\d+)";
}
