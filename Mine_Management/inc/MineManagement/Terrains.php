<?php

namespace MineManagement;

class Terrains {
	private function __construct() {

	}

	public static function get($id = null) {
		$database = Database::getConnection();
		$stmt = null;
		$returnedTerrain = null;
		if (\is_numeric($id)) {
			$stmt = $database->prepare('SELECT id, name, image FROM terrains WHERE id = ?');
			$stmt->bind_param('i', $id);
		}
		else if (\is_string($id)) {
			$stmt = $database->prepare('SELECT id, name, image FROM terrains WHERE name = ?');
			$stmt->bind_param('s', $id);
		}
		else if (\is_null($id)) {
			$stmt = $database->prepare('SELECT id, name, image FROM terrains');
			$returnedTerrain = [];
		}
		$stmt->execute();
		$result = $stmt->get_result();
		while ($row = $result->fetch_assoc()) {
			if (is_array($returnedTerrain)) {
				$returnedTerrain[] = $row;
			}
			else {
				$returnedTerrain = $row;
			}
		}
		return $returnedTerrain;
	}
}
