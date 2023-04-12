<?php
namespace Space\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;
use Gt\Cli\Parameter\Parameter;
use Space\Universe\Generator01UGS;
use Space\Universe\Generator02LGS;
use Space\Universe\Generator03GGS;
use Space\Universe\Generator04SGS;
use Space\Universe\Generator05SPS;

class GenerateCommand extends Command {
	public function run(ArgumentValueList $arguments = null):void {
		$multiverseId = $arguments->get("multiverse");
		$usc = $arguments->get("ugs");
		$locator = implode("@", [$multiverseId, "ugs=$usc"]);

		$generator = new Generator01UGS($locator);

		if($lgs = $arguments->get("lgs", "")->get()) {
			$locator .= " lgs=$lgs";
			$generator = new Generator02LGS($locator, $generator->data);
		}
		if($ggs = $arguments->get("ggs", "")->get()) {
			$locator .= " ggs=$ggs";
			$generator = new Generator03GGS($locator, $generator->data);
		}
		if($sgs = $arguments->get("sgs", "")->get()) {
			$locator .= " sgs=$sgs";
			$generator = new Generator04SGS($locator, $generator->data);
		}
		if($sps = $arguments->get("sps", "")->get()) {
			$locator .= " sps=$sps";
			$generator = new Generator05SPS($locator, $generator->data);
		}
		if($gps = $arguments->get("gps", "")->get()) {
			$locator .= " gps=$gps";
			$generator = new Generator06GPS($locator, $generator->data);
		}
		if($inm = $arguments->get("inm", "")->get()) {
			$locator .= " inm=$inm";
			$generator = new Generator07INM($locator, $generator->data);
		}

		$this->writeLine($generator);
	}

	public function getName():string {
		return "generate";
	}

	public function getDescription():string {
		return "Generate the universe and everything in it";
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
			new Parameter(true, "ugs")
		];
	}

	public function getOptionalParameterList():array {
		return [
			new Parameter(true, "lgs"),
			new Parameter(true, "ggs"),
			new Parameter(true, "sgs"),
			new Parameter(true, "sps"),
			new Parameter(true, "gps"),
			new Parameter(true, "inm"),
		];
	}
}
