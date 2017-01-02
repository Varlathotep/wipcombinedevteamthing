<?php

namespace MineManagement; 
class PlanetTerrains extends Debuggable implements Stored {
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
	//We're going to go ahead and determine if there was an array provided and if that array has more than 1 element.
	//If it does, we need to implode them and then prepare the query. NOTE: ALWAYS CALL THE get METHOD AND NOT THIS
	//METHOD DIRECTLY. THE GET METHOD WILL VERIFY THAT THE IDS ARE INTS AND WILL PREVENT THIS FROM BEING INJECTIBLE.
	if (\is_array($id) && \count($id) > 0) {
	  $query = 'SELECT refid, planetid, terrainid, x, y FROM planetterrains WHERE planetid IN (' . \implode(',', $id) . ')';
	  $stmt = $database->prepare($query);
	}
	$stmt->execute();
	$result = $stmt->get_result();
	//Verify that the results have been returned and begin processing it.
	if ($result) {
	  //Loop over the result set and create the appropriate PlanetTerrains objects with the valuse needed.
	  while ($row = $result->fetch_object('MineManagement\PlanetTerrains')) {
		if (!\array_key_exists($row->y, $returnedTerrain)) {
		  $returnedTerrain[$row->y] = [];
		}
		$returnedTerrain[$row->y][$row->x] = $row;
	  }
	}
	$returnedTerrain = [$returnedTerrain];
	return $returnedTerrain;
  }

  public function delete() {
	//We need to prepare the delete statement and execute it.
	$stmt = $this->_database->prepare('DELETE FROM planetterrains WHERE refid = ?');
	$stmt->bind_param('i', $this->refid);
	$stmt->execute();
  }

  public function update() {
	//We need to prepare the update statement and execute it.
	$stmt = $this->_database->prepare('UPDATE planetterrains SET terrainid = ?, x = ?, y = ? WHERE refid = ?');
	$stmt->bind_param('iiii', $this->terrainid, $this->x, $this->y, $this->refid);
	$stmt->execute();
  }

  public function insert() {
	//We need to prepare the insert statement and execute it.
	$stmt = $this->_database->prepare('INSERT INTO planetterrains (planetid, terrainid, x, y) VALUES (?, ?, ?, ?)');
	$stmt->bind_param('iiii', $this->planetid, $this->terrainid, $this->x, $this->y);
	$stmt->execute();
	$this->refid = $this->_database->insert_id;
  }
}

