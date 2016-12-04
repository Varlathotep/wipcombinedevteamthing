<?php

namespace MineManagement; 
class Planets implements Stored {
	use Commitable;
	private $_database = null;
	public $id = null;
	public $width;
	public $height;
	public $name;
	public $terrain = [];
	public $deposits = [];

	public function __construct($database) {
		$this->_database = $database;
	}
	
	public function update() {
		$this->_database->updatePlanet($this);
	}

	public function insert() {
		$this->_database->insertPlanet($this);
	}
}

