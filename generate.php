<?php
use Gt\Cli\Application;
use Gt\Cli\Argument\CommandArgumentList;
use Space\Command\GenerateCommand;

require "vendor/autoload.php";

$application = new Application(
	"Let's explore",
	new CommandArgumentList("generate", ...$argv),
	new GenerateCommand(),
);
$application->run();
