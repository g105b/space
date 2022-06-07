<?php
namespace Space\Universe;

class Generator05SSC extends AbstractUniverseGenerator {
	public const REGEX = Generator04SGC::REGEX . " ssc=(?P<SSC_Z>[pn][\d.]+):(?P<SSC_LON>[pn][\d.]+)";
}
