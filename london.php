<?php
require "vendor/autoload.php";

$londonFile = "data/universe/default/ugs_+0:+0/fgs_+0:+0/ggs_+0:+0/sgs_+0:+0/solar_Sol/planet_Earth/continent_Eurasia/polity_England/settlement_London/details_London.json";
$data = json_decode(file_get_contents($londonFile));

for($lon = -0.3; $lon < 0.3; $lon += 0.01) {
	for($lat = 51.55; $lat > 51.44; $lat -= 0.01) {
		echo "[     ]";
	}
	echo PHP_EOL;
}
