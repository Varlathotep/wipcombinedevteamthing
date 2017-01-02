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
	//We need to get a database connection from the Database object.
	$this->_database = Database::getConnection();
  }

  public static function select($planetNameOrId = null) {
	$stmt = null;
	$returnedPlanets = [];
	$database = Database::getConnection();
	//We're going to go ahead and determine if there was an array provided and if that array has more than 1 element.
	//If it does, we need to implode them and then prepare the query. NOTE: ALWAYS CALL THE get METHOD AND NOT THIS
	//METHOD DIRECTLY. THE GET METHOD WILL VERIFY THAT THE IDS ARE INTS AND WILL PREVENT THIS FROM BEING INJECTIBLE.
	if (\is_array($planetNameOrId) && \count($planetNameOrId) > 0) {
	  $query = 'SELECT id, name, width, height FROM planets WHERE id IN (' . \implode(',', $planetNameOrId) . ')';
	  $stmt = $database->prepare($query);
	}
	else {
	  $stmt = $database->prepare('SELECT id, name, width, height FROM planets');
	}
	//We need to execute the statement, get the result and determine if the result is set.
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result) {
	  //We need to loop over the result set, fetching objects of the Planets type and settings its values to the correct
	  //state.
	  while ($row = $result->fetch_object('MineManagement\Planets')) {
		$returnedPlanets[] = $row;
		$row->terrain = PlanetTerrains::get($row->id);
		$row->deposits = Materials::get($row->id);
	  }
	}
	return $returnedPlanets;
  }

  public function delete() {
	//We need to prepare the delete query, set its ID, and delete that sun of a gun from the database.
	$stmt = $this->_database->prepare('DELETE FROM planets WHERE id = ?');
	$stmt->bind_param('i', $this->id);
	$stmt->execute();
	//We are committing the terrain and deposits arrays.
	$this->commitTerrain();
	$this->commitDeposits();
  }

  public function update() {
	//We need to prepare the update query, set its values, and update that son of a gun in the database.
	$stmt = $this->_database->prepare('UPDATE planets SET name = ?, width = ?, height = ? WHERE id = ?');
	$stmt->bind_param('siii', $this->name, $this->width, $this->height, $this->id);
	$stmt->execute();
	//We are committing the terrain and deposits arrays.
	$this->commitTerrain();
	$this->commitDeposits();
  }

  public function insert() {
	//We need to prepare the insert query, set its values, and insert that son of a gun into the database.
	$stmt = $this->_database->prepare('INSERT INTO planets (name, width, height) VALUE (?, ?, ?)');
	$stmt->bind_param('sii', $this->name, $this->width, $this->height);
	$stmt->execute();
	//We're retrieving the id from the database and then committing the terrain and deposits arrays.
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
			//If the planet is marked for deletion, then all terrain under it should be as well (this is very
			//likely never going to be needed if foreign keys are properly used).
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
	//This is currently not implemented. It will be soon!
	for ($i = 0; $i < $this->height; $i++) {
	  for ($i2 = 0; $i2 < $this->width; $i2++) {

	  }
	}
  }
}

