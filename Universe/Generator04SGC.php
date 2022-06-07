<?php
namespace Space\Universe;

class Generator04SGC extends AbstractUniverseGenerator {
	public const REGEX = Generator03GGC::REGEX . " sgc=(?P<SGC_X>[pn]\d+):(?P<SGC_Y>[pn]\d+)";
}
