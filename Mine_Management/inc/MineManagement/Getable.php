<?php

namespace MineManagement;

trait Getable {
  public static function get($ids = []) {
	$isSingleId = \is_numeric($ids);
	$resultSet = null;
	if ($isSingleId) {
	  $resultSet = self::select(array_map(function ($id) { return (int)$id; }, [$ids]));
	  if (\count($resultSet) == 1) {
		$resultSet = $resultSet[0];
	  }
	  else {
		$resultSet = null;
	  }
	}
	else {
	  $resultSet = self::select(array_map(function ($id) { return(int)$id; }, $ids));
	}
	return $resultSet;
  }
}
