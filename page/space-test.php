<?php
use Gt\Dom\HTMLDocument;
use Gt\DomTemplate\DocumentBinder;
use Gt\DomTemplate\ListElementCollection;

function go(
	HTMLDocument $document,
	DocumentBinder $binder,
	ListElementCollection $listElementCollection,
):void {
	for($y = 0; $y <= 9; $y++) {
		for($x = 0; $x <= 9; $x++) {
			$el = $listElementCollection->get($document)->insertListItem();
			$binder->bindKeyValue("x", $x, $el);
			$binder->bindKeyValue("y", $y, $el);
		}
	}
}
