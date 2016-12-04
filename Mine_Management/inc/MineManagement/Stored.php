<?php

namespace MineManagement;

interface Stored {
	function update();
	function insert();
	function remove();
	static function get($id = null);
}
