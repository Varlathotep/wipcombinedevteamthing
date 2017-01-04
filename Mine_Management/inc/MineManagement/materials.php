<?php

namespace MineManagement;

class Materials {
  use Getable;

  public static function select($ids) {
	$database = Database::getConnection();
	$stmt = null;
	$returnedMaterial = [];
	if (\is_array($ids) && \count($ids) > 0) {
	  $query = 'SELECT id, name, image FROM materials WHERE id IN (' . \implode(',', $ids) . ')';
	  $stmt = $database->prepare($query);
	}
	else {
	  $stmt = $database->prepare('SELECT id, name, image FROM materials');
	}
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result) {
	  while ($row = $result->fetch_assoc()) {
		$returnedMaterial[] = $row;
	  }
	}
	return $returnedMaterial;
  }
}
