<?php

use Gt\Ulid\Ulid;

require "vendor/autoload.php";

for($i = 0; $i < 20; $i++) {
	echo $i . " - ";
	echo new Ulid();
	echo PHP_EOL;
}
