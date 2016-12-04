<?php

namespace MineManagement {
	class Planets {
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
	
		public function commit() {
			if (!is_null($this->id)) {
				$this->_database->updatePlanet($this);
			}
			else {
				$this->_database->insertPlanet($this);
			}
		}
	}
}
