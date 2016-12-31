<?php

namespace MineManagement; 
class PlanetTerrains implements Stored {
  use Commitable;
  use Getable;

  public $refid;
  public $planetid;
  public $terrainid;
  public $x;
  public $y;
  private $_database = null;

  public function __construct() {
	$this->_database = Database::getConnection();
  }

  public static function select($id = null) {
	$database = Database::getConnection();
	$returnedTerrain = [];
	$stmt = null;
	if (\is_array($id) && \count($id) > 0) {
	  $query = 'SELECT refid, planetid, terrainid, x, y FROM planetterrains WHERE planetid IN (' . \implode(',', $id) . ')';
	  $stmt = $database->prepare($query);
	}
	$stmt->execute();
	$result = $stmt->get_result();
	while ($row = $result->fetch_object('MineManagement\PlanetTerrains')) {
	  if (!\array_key_exists($row->y, $returnedTerrain)) {
		$returnedTerrain[$row->y] = [];
	  }
	  $returnedTerrain[$row->y][$row->x] = $row;
	}
	$returnedTerrain = [$returnedTerrain];
	return $returnedTerrain;
  }

  public function delete() {
	$stmt = $this->_database->prepare('DELETE FROM planetterrains WHERE refid = ?');
	$stmt->bind_param('i', $this->refid);
	$stmt->execute();
  }

  public function update() {
	$stmt = $this->_database->prepare('UPDATE planetterrains SET terrainid = ?, x = ?, y = ? WHERE refid = ?');
	$stmt->bind_param('iii', $this->terrainid, $this->x, $this->y, $this->refid);
	$stmt->execute();
  }

  public function insert() {
	$stmt = $this->_database->prepare('INSERT INTO planetterrains (planetid, terrainid, x, y) VALUES (?, ?, ?, ?)');
	$stmt->bind_param('iiii', $this->planetid, $this->terrainid, $this->x, $this->y);
	$stmt->execute();
	$this->refid = $this->_database->insert_id;
  }
}

