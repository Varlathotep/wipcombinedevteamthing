<?php

namespace MineManagement; 
class Terrains implements Stored {
	use Commitable;
	public $refid;
	public $planetid;
	public $terrainid;
	public $x;
	public $y;
	public $image;
	private $_database = null;

	public function __construct($database) {
		$this->_database = $database;
	}

	public function update() {

	}

	public function insert() {

	}
}

