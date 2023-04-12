<?php
use Gt\Input\Input;
use Space\Noise\Simplex;
use g105b\drng\Random;
use g105b\drng\StringSeed;
use Space\Noise\StarGenerator;

function go(Input $input):void {
	$random = new Random(new StringSeed($input->getString("seed") ?? "default string"));

	$seed = [];
	for($i = 0; $i < 256; $i++) {
		array_push($seed, $random->getInt(0, 256));
	}
	$simplex = new Simplex(...$seed);
	$starGenerator = new StarGenerator($simplex);
	$starGenerator->outputPng(
		$input->getInt("x") ?? 0,
		$input->getInt("y") ?? 0,
	);
}
