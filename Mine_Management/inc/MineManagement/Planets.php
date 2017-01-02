<?php

namespace MineManagement; 
class Planets extends Debuggable implements Stored {
  use Commitable;
  use Getable;

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

  public static function select($planetNameOrId = null) {
	$stmt = null;
	$returnedPlanets = [];
	$database = Database::getConnection();
	//We need to determine whether or not the passed value is an int, string, or null, then prepare the correct statement for it.
	if (\is_array($planetNameOrId) && \count($planetNameOrId) > 0) {
	  $query = 'SELECT id, name, width, height FROM planets WHERE id IN (' . \implode(',', $planetNameOrId) . ')';
	  $stmt = $database->prepare($query);
	}
	else {
	  $stmt = $database->prepare('SELECT id, name, width, height FROM planets');
	}
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result) {
	  while ($row = $result->fetch_object('MineManagement\Planets')) {
		$returnedPlanets[] = $row;
		$row->terrain = PlanetTerrains::get($row->id);
		$row->deposits = Materials::get($row->id);
	  }
	}
	return $returnedPlanets;
  }

  public function delete() {
	$stmt = $this->_database->prepare('DELETE FROM planets WHERE id = ?');
	$stmt->bind_param('i', $this->id);
	$stmt->execute();
	$this->commitTerrain();
	$this->commitDeposits();
  }

  public function update() {
	$stmt = $this->_database->prepare('UPDATE planets SET name = ?, width = ?, height = ? WHERE id = ?');
	$stmt->bind_param('siii', $this->name, $this->width, $this->height, $this->id);
	$stmt->execute();
	$this->commitTerrain();
	$this->commitDeposits();
  }

  public function insert() {
	$stmt = $this->_database->prepare('INSERT INTO planets (name, width, height) VALUE (?, ?, ?)');
	$stmt->bind_param('sii', $this->name, $this->width, $this->height);
	$stmt->execute();
	$this->id = $this->_database->insert_id;
	$this->commitTerrain();
	$this->commitDeposits();
  }

  private function commitTerrain() {
	//We need to make sure that terrain is an actual array. if not, we have a problem!
	if (\is_array($this->terrain)) {
	  for ($i = 0; $i < $this->height; $i++) {
		for ($i2 = 0; $i2 < $this->width; $i2++) {
		  //We need to make sure the coordinates exist. If they do, we can do the manipulations we need to do.
		  if (\array_key_exists($i, $this->terrain) && \array_key_exists($i2, $this->terrain[$i])) {
			$terrain = $this->terrain[$i][$i2];
			$terrain->planetid = $this->id;
			if ($this->_markForDelete) {
			  $terrain->markForDelete();
			}
			$terrain->commit();
		  }
		}
	  }
	}
  }

  private function addTerrain($terrainId, $x, $y) {
	//We need to determine if the array key is present. If not, we need to create it, then
	//we're able to create the terrain object.
	if (!\is_array($this->terrain)) {
	  $this->terrain = [];
	}
	if (!\array_key_exists($y, $this->terrain)) {
	  $this->terrain[$y] = [];
	}
	$workingTerrain = new PlanetTerrains();
	$workingTerrain->terrainid = (int)$terrainId;
	$workingTerrain->x = (int)$x;
	$workingTerrain->y = (int)$y;
	$this->terrain[$y][$x] = $workingTerrain;
  }

  public function deleteTerrain($x, $y) {
	//This isn't the best option here, but it allows us to silently delete broken planets (which
	//is preferred over crashing!).
	if (\array_key_exists($y, $this->terrain) && \array_key_exists($x, $this->terrain[$y])) {
	  $this->terrain[$y][$x]->markForDelete();
	}
  }

  public function updateTerrain($terrainId, $x, $y) {
	//We need to verify that these keys exist. If they don't, we need to add them. This is sort
	//of a generic method used to set up the planet so that I don't have to do it in line.
	if (\array_key_exists($y, $this->terrain) && \array_key_exists($x, $this->terrain[$y])) {
	  $this->terrain[$y][$x]->terrainid = (int)$terrainId;
	}
	else {
	  $this->addTerrain($terrainId, $x, $y);
	}
  }

  private function commitDeposits() {
	for ($i = 0; $i < $this->height; $i++) {
	  for ($i2 = 0; $i2 < $this->width; $i2++) {

	  }
	}
  }
}

