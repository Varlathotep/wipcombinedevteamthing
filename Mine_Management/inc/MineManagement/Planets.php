<?php

namespace MineManagement; 
class Planets implements Stored {
	use Commitable;
	private $_database = null;
	public $id = null;
	public $width;
	public $height;
	public $name;
	public $terrain = [];
	public $deposits = [];

	public function __construct() {
		$this->_database = Database::getConnection();
	}

	public static function get($planetNameOrId = null) {
		$stmt = null;
		$returnedPlanets = null;
		$database = Database::getConnection();
		//We need to determine whether or not the passed value is an int, string, or null, then prepare the correct statement for it.
		if (\is_numeric($planetNameOrId)) {
			$stmt = $database->prepare('SELECT id, name, width, height FROM planets WHERE id = ?;');
			$stmt->bind_param('i', $planetNameOrId);
		}
		else if (\is_string($planetNameOrId)) {
			$stmt = $database->prepare('SELECT id, name, width, height FROM planets WHERE name = ?;');
			$stmt->bind_param('s', $planetNameOrId);
		}
		else {
			$stmt = $database->prepare('SELECT id, name, width, height FROM planets;');
			$returnedPlanets = [];
		}
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result) {
			while ($row = $result->fetch_object('MineManagement\Planets')) {
				if (is_array($returnedPlanets)) {
					$returnedPlanets[] = $row;
				}
				else {
					$returnedPlanets = $row;
				}
				$row->terrain = PlanetTerrains::get($row->id);
				$row->deposits = Materials::get($row->id);
			}
		}
		return $returnedPlanets;
	}

	public function remove() {
		$stmt = $this->_database->prepare('DELETE FROM planets WHERE id = ?;');
		$stmt->bind_param('i', $this->id);
		$stmt->execute();
	}

	public function update() {
		$stmt = $this->_database->prepare('UPDATE planets SET name = ?, width = ?, height = ? WHERE id = ?;');
		$stmt->bind_param('siii', $this->name, $this->width, $this->height, $this->id);
		$stmt->execute();
	}

	public function insert() {
		$stmt = $this->_database->prepare('INSERT INTO planets (name, width, height) VALUE (?, ?, ?);');
		$stmt->bind_param('sii', $this->name, $this->width, $this->height);
		$stmt->execute();
		$this->id = $this->_database->insert_id;
		$this->commitTerrain();
	}

	private function commitTerrain() {
		for ($i = 0, $l = $this->height; $i < $l; $i++) {
			for ($i2 = 0; $i2 < $l; $i2++) {
				$terrain = $this->terrain[$i][$i2];
				$terrain->planetid = $this->id;
				$terrain->commit();
			}
		}
	}
}

