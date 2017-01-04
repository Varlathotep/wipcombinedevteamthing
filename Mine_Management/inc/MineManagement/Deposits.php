<?php

namespace MineManagement;
class Deposits implements Stored {
  use Commitable;
  use Getable;

  public $id;
  public $planetid;
  public $terrainid;
  public $materialid;
  public $quantity;
  private $_database;

  public function __construct() {
	$this->_database = Database::getConnection();
  }

  public static function select($id = null) {
	$database = Database::getConnection();
	$returnedDeposits = [];
	$stmt = null;
	if (\is_array($id) && \count($id) > 0) {
	  $query = 'SELECT id, planetid, terrainid, materialid, quantity FROM deposits WHERE terrainid IN (' . \implode(',', $id) . ')';
	  $stmt = $database->prepare($query);
	}
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result) {
	  while ($row = $result->fetch_object('MineManagement\Deposits')) {
		$returnedDeposits[$row->terrainid] = $row;
	  }
	}
	return [$returnedDeposits];
  }

  public function delete() {
	$stmt = $this->_database->prepare('DELETE FROM deposits WHERE id = ?');
	$stmt->bind_param('i', $this->id);
	$stmt->execute();
  }

  public function insert() {
	$stmt = $this->_database->prepare('INSERT INTO deposits (planetid, terrainid, materialid, quantity) VALUES (?, ?, ?, ?)');
	$stmt->bind_param('iiii', $this->planetid, $this->terrainid, $this->materialid, $this->quantity);
	$stmt->execute();
  }

  public function update() {
	$stmt = $this->_database->prepare('UPDATE deposits SET materialid = ?, quantity = ? WHERE terrainid = ?');
	$stmt->bind_param('iii', $this->materialid, $this->quantity, $this->terrainid);
	$stmt->execute();
  }
}
