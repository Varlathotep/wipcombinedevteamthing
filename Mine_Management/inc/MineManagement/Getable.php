<?php

namespace MineManagement;

trait Getable {
  public static function get($ids = []) {
	$isSingleId = \is_numeric($ids);
	$resultSet = null;
	if ($isSingleId) {
	  $resultSet = self::select([$ids]);
	  if (\count($resultSet) == 1) {
		$resultSet = $resultSet[0];
	  }
	  else {
		$resultSet = null;
	  }
	}
	else {
	  $resultSet = self::select($ids);
	}
	return $resultSet;
  }

  protected static function generateInParamStatement($query, $parameters) {
	$paramDef = \gettype($parameters[0])[0];
	$query .= 'IN (?';
	$params = [null];
	$params[] = $parameters[0];
	for ($i = 1, $l = \count($parameters); $i < $l; $i++) {
	  $paramDef .= \gettype($parameters[$i])[0];
	  $query .= ', ?';
	  $params[] = $parameters[$i];
	}
	$params[0] = $paramDef;
	$query .= ')';
	return [$query, $params];
  }

  protected static function refParams($params) {
	$refs = [];
	foreach ($params as &$value) {
	  $refs[] = $value;
	  unset($value);
	}
	return $refs;
  }
}
