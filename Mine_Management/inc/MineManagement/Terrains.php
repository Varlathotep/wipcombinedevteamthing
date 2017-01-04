<?php

namespace MineManagement;

class Terrains {
  use Getable;

  public static function select($ids) {
	$database = Database::getConnection();
	$stmt = null;
	$returnedTerrain = [];
	//We're going to go ahead and determine if there was an array provided and if that array has more than 1 element.
	//If it does, we need to implode them and then prepare the query. NOTE: ALWAYS CALL THE get METHOD AND NOT THIS
	//METHOD DIRECTLY. THE GET METHOD WILL VERIFY THAT THE IDS ARE INTS AND WILL PREVENT THIS FROM BEING INJECTIBLE.
	if (\is_array($ids) && \count($ids) > 0) {
	  $query = 'SELECT id, name, image FROM terrains WHERE id IN (' . \implode(',', $ids) . ')';
	  $stmt = $database->prepare($query);
	}
	else {
	  $stmt = $database->prepare('SELECT id, name, image FROM terrains');
	}
	$stmt->execute();
	$result = $stmt->get_result();
	//Determine if the result has been returned and, if so, we need to iterate over it and fetch an assoc array.
	if ($result) {
	  while ($row = $result->fetch_assoc()) {
		$returnedTerrain[] = $row;
	  }
	}
	return $returnedTerrain;
  }
}
