<?php

namespace MineManagement; 
class Database {
  private static $_conn = null;

  public static function createConnection($hostname, $username, $password, $database) {
	SELF::$_conn = new \mysqli($hostname, $username, $password, $database);
  }

  public static function getConnection() {
	return SELF::$_conn;
  }

  //Everything after this point needs to be migrated to their appropriate object.
  public function insertDeposit($deposit) {
	$depositStatement = $this->_conn->prepare('INSERT INTO deposits (planetid, terrainrefid, materialid, quantity) VALUES (?, ?, ?, ?);');
	$depositStatement->bind_param('iiii', $deposit->planetid, $deposit->terrainrefid, $deposit->materialid, $deposit->quantity);
	$depositStatement->execute();
	$deposit->id = $this->_conn->insert_id;
  }
  
  public function selectPlanetDeposit($planetId) {
	$returnedResult = [];
	$depositStatement = $this->_conn->prepare('SELECT id, planetid, terrainid, materialid, quantity FROM deposits WHERE planetid = ?;');
	$depositStatement->bind_param('i', $planetId);
	$depositStatement->execute();
	$result = $depositStatement->get_result();
	while ($row = $result->fetch_object('MineManagement\Materials', [ $this ])) {
	  $returnedResult[] = $row;
	}
	return $returnedResult;
  }

  public function selectDeposit($depositId) {
	$returnedResult = [];
	$depositStatement = $this->_conn->prepare('SELECT id, planetid, terrainrefid, materialid, quantity FROM deposits WERE id = ?;');
	$depositStatement->bind_param('i', $depositId);
	$depositStatement->execute();
	$result = $depositStatement->get_result();
	while ($row = $result->fetch_object('MineManagement\Materials', [ $this ])) {
	  $returnedResult[] = $row;
	}
	return $returnedResult;
  }

  public function deleteDeposit($depositId) {
	$depositStatement = $this->_conn->prepare('DELETE FROM deposits WHERE id = ?;');
	$depositStatement->bind_param('i', $depositId);
	$depositStatement->execute();
  }

  public function deletePlanetDeposit($planetId) {
	$depositStatement = $this->_conn->prepare('DELETE FROM deposits WHERE planetid = ?;');
	$depositStatement->bind_param('i', $planetId);
	$depositStatement->execute();
  }

  public function deleteMaterialId($materialId) {
	$depositStatement = $this->_conn->prepare('DELETE FROM deposits WHERE materialid = ?;');
	$depositStatement->bind_param('i', $materialId);
	$depositStatement->execute();
  }

  public function updateDeposit($deposit) {
	$depositStatement = $this->_conn->prepare('UPDATE deposits SET planetid = ?, terrainrefid = ?, materialid = ?, quantity = ? WHERE id = ?;');
	$depositStatement->bind_param('iiiii', $deposit->planetid, $deposit->terrainrefid, $deposit->materialid, $deposit->quantity, $deposit->id);
	$depositStatement->execute();
  }
}

