<?php

namespace MineManagement;

trait Getable {
  public static function get($ids = []) {
	//Determines if a single ID was provided. If not, we know to just continue passing it into the select method.
	$isSingleId = \is_numeric($ids);
	$resultSet = null;
	if ($isSingleId) {
	  //We need to wrap the ID in an array and then unpack it once it has been returned.
	  $resultSet = self::select(array_map(function ($id) { return (int)$id; }, [$ids]));
	  if (\count($resultSet) == 1) {
		$resultSet = $resultSet[0];
	  }
	  else {
		$resultSet = null;
	  }
	}
	else {
	  //We need to execute the select statement and return the value.
	  $resultSet = self::select(array_map(function ($id) { return(int)$id; }, $ids));
	}
	return $resultSet;
  }
}
