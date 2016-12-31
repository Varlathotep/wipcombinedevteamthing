<?php

namespace MineManagement;

class Terrains {
	use Getable;

	public static function select($id) {
		$database = Database::getConnection();
		$stmt = null;
		$returnedTerrain = [];
		if (\is_array($id) && \count($id) > 0) {
			$query = 'SELECT id, name, image FROM terrains WHERE id ';
			$params = null;
			list($query, $params) = self::generateInParamStatement($query, $id);
			$stmt = $database->prepare($query);
			\call_user_func_array([$stmt, 'bind_param'], self::refParams($params));
		}
		else {
			$stmt = $database->prepare('SELECT id, name, image FROM terrains');
		}
		$stmt->execute();
		$result = $stmt->get_result();
		while ($row = $result->fetch_assoc()) {
			$returnedTerrain[] = $row;
		}
		return $returnedTerrain;
	}
}
