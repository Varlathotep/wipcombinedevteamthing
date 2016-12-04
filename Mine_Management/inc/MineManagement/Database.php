<?php

namespace MineManagement {
	class Database {
		private $_conn = null;

		/**
			Creates the Database object which will be used by multiple other classes to manage their database calls.
			@param		$hostname		The host of the mysql database.
			@param		$username		The username of the mysql database.
			@param		$password		The password of the mysql database.
			@param		$database		The database being accessed.
		*/
		public function __construct($hostname, $username, $password, $database) {
			$this->_conn = new \mysqli($hostname, $username, $password, $database);
		}

		/**
			This will populate the database with an initial set of tables and some base data for material types and terrain types.
		*/
		public function initializeDatabase() {
			$this->_conn->query(file_get_contents('inc/initialdata.sql'));
		}

		/**
			This will insert a new planet into the database with the given name, width, height and sorted single dimension array of Terrains objects.
			@param		$planet			A Planets object containing the definition of the newly created planet.
		*/
		public function insertPlanet($planet) {
			$planetStatement = $this->_conn->prepare('INSERT INTO planets (name, width, height) VALUE (?, ?, ?);');
			$terrainStatement = $this->_conn->prepare('INSERT INTO planetterrains (planetid, terrainid, x, y) VALUE (?, ?, ?, ?);');
			$planetStatement->bind_param('sii', $planet->name, $planet->width, $planet->height);
			$planetStatement->execute();
			$planet->id = $this->_conn->insert_id;
			//We need to loop over the planet's terrain so that they can all be entered into the database sequentially. The site itself doesn't care if the data has an X and Y value.
			for ($i = 0, $l = $planet->height; $i < $l; $i++) {
				for ($i2 = 0; $i2 < $l; $i2++) {
					$terrain = $planet->terrain[$i][$i2];
					$terrain->planetid = $planet->id;
					$terrainStatement->bind_param('iiii', $terrain->planetid, $terrain->terrainid, $terrain->x, $terrain->y);
					$terrainStatement->execute();
					$terrain->refid = $this->_conn->insert_id;
				}
			}
		}

		/**
			This will select a planet from the database with either the given name or id and its sorted single dimension array of Terrain objects.
			@param		$planetNameOrId	The name or ID of the planet being searched for.
			@return						An array of planets matching the search criteria.
		*/
		public function selectPlanet($planetNameOrId) {
			$planetStatement = null;
			$terrainStatement = $this->_conn->prepare('SELECT refid, planetid, terrainid, image FROM planetterrains INNER JOIN terrains ON id = terrainid WHERE planetid = ? ORDER BY refid;');
			$returnedPlanets = [];
			//We need to determine whether or not the passed value is an int, string, or null, then prepare the correct statement for it.
			if (\is_numeric($planetNameOrId)) {
				$planetStatement = $this->_conn->prepare('SELECT id, name, width, height FROM planets WHERE id = ?;');
				$planetStatement->bind_param('i', $planetNameOrId);
			}
			else if (\is_string($planetNameOrId)) {
				$planetStatement = $this->_conn->prepare('SELECT id, name, width, height FROM planets WHERE name = ?;');
				$planetStatement->bind_param('s', $planetNameOrId);
			}
			else {
				$planetStatement = $this->_conn->prepare('SELECT id, name, width, height FROM planets;');
			}
			$planetStatement->execute();
			$result = $planetStatement->get_result();
			//We're checking to make sure the statement executed properly. We'll need to do some error handling after this maybe?
			if ($result) {
				//We're going to loop over the result set and put the data into a "MineManagement\Planets" object.
				while ($row = $result->fetch_object('MineManagement\Planets', [ $this ])) {
					$returnedPlanets[] = $row;
					$terrainStatement->bind_param('i', $row->id);
					$terrainStatement->execute();
					$terrainResult = $terrainStatement->get_result();
					//Similar to the planet loop, we're going to loop over the result set and put the data into a "MineManagement\Terrains" object.
					$this->selectPlanetTerrain($row);
					$row->deposits = $this->selectPlanetDeposit($row->id);
				}
			}
			return $returnedPlanets;
		}

		/**
			This will delete a SINGLE planet from the database. If you wish to empty the database, please use Database::deleteAllPlanets(void) instead.
			@param		$planetNameOrId	The name or ID of the planet being searched for.
		*/
		public function deletePlanet($planet) {
			$planetStatement = null;
			//We need to determine whether or not the passed value is an int, string or null, then prepare the correct statement or throw an error in the case of a null/object.
			if (\is_numeric($planet)) {
				$planetStatement = $this->_conn->prepare('DELETE FROM planets WHERE id = ?;');
				$planetStatement->bind_param('i', $planet);
			}
			else if (\is_string($planet)) {
				$planetStatement = $this->_conn->prepare('DELETE FROM planets WHERE name = ?;');
				$planetStatement->bind_param('s', $planet);
			}
			else if (\is_object($planet)) {
				$planetStatement = $this->_conn->prepare('DELETE FROM planets WHERE id = ?;');
				$planetStatement->bind_param('i', $planet->id);
			}
			else {
				throw new \Exception('No ID or planet name was provided! This would delete all planets in the database! Please use deleteAllPlanets for this!');
			}
			//We only need to execute the statement. Nothing will be returned outside of the number of rows deleted, which isn't too hugely important.
			$planetStatement->execute();
		}

		/**
			This will update a SINGLE planet from the database.
			@param		$planet	The planet object containing the definition of the planet.
		*/
		public function updatePlanet($planet) {
			$planetStatement = $this->_conn->prepare('UPDATE planets SET name = ?, width = ?, height = ? WHERE id = ?;');
			$planetStatement->bind_param('siii', $planet->name, $planet->width, $planet->height, $planet->id);
			$planetStatement->execute();
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
