<?php
namespace Space\Universe;

class Generator01USC extends AbstractUniverseGenerator {
	public const REGEX = AbstractUniverseGenerator::REGEX . "usc=(?P<USC_X>[pn]\d+):(?P<USC_Y>[pn]\d+)";
}
