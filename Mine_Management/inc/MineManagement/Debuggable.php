<?php

namespace MineManagement;
class Debuggable {
  public function __debugInfo() {
	$properties = \get_object_vars($this);
	return $properties;
  }
}
