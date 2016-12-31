<?php

namespace MineManagement {
  class Database {
	private static $_conn = null;

		/**
			Creates the Database object which will be used by multiple other classes to manage their database calls.
			@param		$hostname		The host of the mysql database.
			@param		$username		The username of the mysql database.
			@param		$password		The password of the mysql database.
			@param		$database		The database being accessed.
		 */
	public static function createConnection($hostname, $username, $password, $database) {
	  SELF::$_conn = new \mysqli($hostname, $username, $password, $database);
	}

	public static function getConnection() {
	  return SELF::$_conn;
	}

	public function insertTerrain($terrain) {
	  $terrainStatement = $this->_conn->prepare('INSERT INTO planetterrains (planetid, terrainid, x, y) VALUES (?, ?, ?, ?);');
	  $terrainStatement->bind_param('iiii', $terrain->planetid, $terrain->terrainid, $terrain->x, $terrain->y);
	  $terrainStatement->execute();
	  $terrainStatement->refid = $this->_conn->insert_id;
	}

	public function updateTerrain($terrain) {
	  $terrainStatement = $this->_conn->prepare('UPDATE planetterrains SET planetid = ?, terrainid = ?, x = ?, y = ? WHERE refid = ?;');
	  $terrainStatement->bind_param('iiiii', $terrain->planetid, $terrain->terrainid, $terrain->x, $terrain->y, $terrain->refid);
	  $terrainStatement->execute();
	}

	public function deleteTerrain($terrain) {
	  $terrainStatement = $this->_conn->prepare('DELETE FROM planetterrains WHERE refid = ?;');
	  $terrainStatement->bind_param('i', $terrain->refid);
	  $terrainStatement->execute();
	}

	public function deletePlanetTerrain($terrain) {
	  $terrainStatement = $this->_conn->prepare('DELETE FROM planetterrains WHERE planetid = ?;');
	  $terrainStatement->bind_param('i', $terrain->planetid);
	  $terrainStatement->execute();
	}

	public function selectPlanetTerrain($planet) {
	  $terrainStatement = $this->_conn->prepare('SELECT refid, planetid, image, x, y FROM planetterrains INNER JOIN terrains ON id = terrainid WHERE planetid = ?;');
	  $terrainStatement->bind_param('i', $planet->id);
	  $terrainStatement->execute();
	  $terrainResult = $terrainStatement->get_result();
	  while ($row = $terrainResult->fetch_object('MineManagement\Terrains', [ $this ])) {
		if (!array_key_exists($row->y, $planet->terrain)) {
		  $planet->terrain[$row->y] = [];
		}
		$planet->terrain[$row->y][$row->x] = $row;
	  }
	}

	public function selectTerrainTypes() {
	  $terrainTypes = $this->_conn->prepare('SELECT * FROM terrains;');
	  $terrainTypes->execute();
	  $result = $terrainTypes->get_result();
	  $return = [];
	  while ($row = $result->fetch_assoc()) {
		$return[] = $row;
	  }
	  return $return;
	}

		/**
			This will insert a new deposit for the given planet and terrain reference ID of the material type and quantity specified.
			@param		$deposit		A Materials object containing the information needing to be inserted.
		 */
	public function insertDeposit($deposit) {
	  $depositStatement = $this->_conn->prepare('INSERT INTO deposits (planetid, terrainrefid, materialid, quantity) VALUES (?, ?, ?, ?);');
	  $depositStatement->bind_param('iiii', $deposit->planetid, $deposit->terrainrefid, $deposit->materialid, $deposit->quantity);
	  $depositStatement->execute();
	  $deposit->id = $this->_conn->insert_id;
	}

		/**
			This will select all deposits based off of a planet ID.
			@param		$planetId		The planet ID to search by.
			@return						An array containing all deposit entries.
		 */
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

		/**
			This will select all deposits based off of a material ID.
			@param		$materialId		The material ID to search by.
			@return						An array containing all deposit entries.
		 */
	public function selectMaterialDeposit($materialId) {
	  $returnedResult = [];
	  $depositStatement = $this->_conn->prepare('SELECT id, planetid, terrainrefid, materialid, quantity FROM deposits WHERE materialid = ?;');
	  $depositStatement->bind_param('i', $materialId);
	  $depositStatement->execute();
	  $result = $depositStatement->get_result();
	  while ($row = $result->fetch_object('MineManagement\Materials', [ $this ])) {
		$returnedResult[] = $row;
	  }
	  return $returnedResult;
	}

		/**
			This will select all deposits based off of a deposit ID.
			@param		$depositId		The deposit ID to search by.
			@return						An array containing all deposit entries.
		 */
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
}
