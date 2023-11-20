<?php
namespace Space\Actor\Location;

/**
 * The location hierarchy is as follows:
 *
 * Cell - A location where the player can stand.
 * Container - where the cell is: Room / Ground (terrestrial unspecified location) / Sector (outer-space unspecified location)
 */
class Cell {
	public function __construct(
		public string $id,
	) {}
}
