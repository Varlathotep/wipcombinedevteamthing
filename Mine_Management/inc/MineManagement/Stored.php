<?php

namespace MineManagement;

interface Stored {
	function update();
	function insert();
	function remove();
	static function select($id = null);
}
