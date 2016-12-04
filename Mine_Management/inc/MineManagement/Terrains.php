<?php

namespace MineManagement {
	class Terrains {
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
	}
}
