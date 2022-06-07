<?php
namespace Space\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;
use Gt\Cli\Parameter\Parameter;
use Space\Universe\Generator01USC;
use Space\Universe\Generator02LGC;
use Space\Universe\Generator03GGC;
use Space\Universe\Generator04SGC;
use Space\Universe\Generator05SSC;

class GenerateCommand extends Command {
	public function run(ArgumentValueList $arguments = null):void {
		$multiverseId = $arguments->get("multiverse");
		$usc = $arguments->get("usc");
		$locator = implode("@", [$multiverseId, "usc=$usc"]);

		$generator = new Generator01USC($locator);

		if($lgc = $arguments->get("lgc", "")->get()) {
			$locator .= " lgc=$lgc";
			$generator = new Generator02LGC($locator);
		}
		if($ggc = $arguments->get("ggc", "")->get()) {
			$locator .= " ggc=$ggc";
			$generator = new Generator03GGC($locator);
		}
		if($sgc = $arguments->get("sgc", "")->get()) {
			$locator .= " sgc=$sgc";
			$generator = new Generator04SGC($locator);
		}
		if($ssc = $arguments->get("ssc", "")->get()) {
			$locator .= " ssc=$ssc";
			$generator = new Generator05SSC($locator);
		}
		if($ods = $arguments->get("ods", "")->get()) {
			$locator .= " ods=$ods";
			$generator = new Generator06ODS($locator);
		}
		if($gps = $arguments->get("gps", "")->get()) {
			$locator .= " gps=$gps";
			$generator = new Generator08GPS($locator);
		}

		$this->writeLine($generator);
	}

	public function getName():string {
		return "generate";
	}

	public function getDescription():string {
		return "Generate the universe and all its stars";
	}

	public function getRequiredNamedParameterList():array {
		return [];
	}

	public function getOptionalNamedParameterList():array {
		return [];
	}

	public function getRequiredParameterList():array {
		return [
			new Parameter(true, "multiverse"),
			new Parameter(true, "usc")
		];
	}

	public function getOptionalParameterList():array {
		return [
			new Parameter(true, "lgc"),
			new Parameter(true, "ggc"),
			new Parameter(true, "sgc"),
			new Parameter(true, "ssc"),
			new Parameter(true, "ods"),
			new Parameter(true, "gps"),
		];
	}
}
