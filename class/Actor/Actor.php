<?php
namespace Space\Actor;

use Space\Actor\Location\Cell;
use Space\Data\IniSerializable;

class Actor implements IniSerializable {
	public function __construct(
		public string $id,
		public Cell $position,
	) {}

	public function iniSerialize():string {
		$assocIni = [];
		$assocIni["details"] = [];

		$str = "";

		foreach($assocIni as $section => $kvp) {
			if($str) {
				$str .= "\n";
			}
			$str .= "[$section]\n";

			foreach($kvp as $key => $value) {
				$str .= "$key=$value\n";
			}
		}

		return $str;
	}
}
