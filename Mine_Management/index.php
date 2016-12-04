<?php
<<<<<<< HEAD
namespace MineManagement; 
error_reporting(E_ALL & ~E_DEPRECATED);
include('inc/autoloader.php');
$route = [];
//Preparing the route for construction.
if (\array_key_exists('q', $_GET)) {
	$route = \explode('/', \trim($_GET['q'], "/\t\n\r\0\x0B"));
}
//We're going to set up the database connection and then we're going to go to town.
Database::createConnection('localhost', 'user', 'password', 'minemanagement2');
if (\count($route) === 0) {
	require_once('page/index.html');
}
else if ($route[0] == 'planets') {
	if ($route[1] == 'return') {
		if ($route[2] == 'all') {
			echo \json_encode(Planets::get());
=======
namespace MineManagement {
	ini_set('always_populate_raw_post_data', -1);
	//This is to fix an issue caused by my local INI. It was throwing a deprecated error and the above didn't fix it.
	error_reporting(E_ALL & ~E_DEPRECATED);
	include('inc/autoloader.php');
	$route = [];
	//Preparing the route for construction.
	if (\array_key_exists('q', $_GET)) {
		$route = \explode('/', \trim($_GET['q'], "/\t\n\r\0\x0B"));
	}
	//We're going to set up the database connection and then we're going to go to town.
	$database = new Database('localhost', 'user', 'pass', 'minemanagement2');
	if (count($_POST) > 0 && array_key_exists('planetEditor', $_POST)) {
		$planet = new Planets($database);
		$planet->name = $_POST['name'];
		$planet->width = $_POST['size'];
		$planet->height = $_POST['size'];
		for ($i = 0, $l = $planet->height; $i < $l; $i++) {
			$array = [];
			$planet->terrain[] = $array;
			for ($i2 = 0; $i2 < $l; $i2++) {
				$terrain = new Terrains($database);
				$terrain->x = $i2;
				$terrain->y = $i;
				$terrain->terrainid = $_POST['planetGrid'][$i][$i2];
				$planet->terrain[$i][$i2] = $terrain;
			}
>>>>>>> origin/master
		}
		else if (\is_numeric($route[2])) {
			echo \json_encode(Planets::get(\intval($route[2])));
		}
	}
	else if ($route[1] == 'add' && \count($_POST) > 0 && \array_key_exists('planetEditor', $_POST) && empty($_POST['planetid'])) {
			$planet = new Planets();
			$planet->name = $_POST['name'];
			$planet->width = $_POST['size'];
			$planet->height = $_POST['size'];
			for ($i = 0, $l = $planet->height; $i < $l; $i++) {
				$array = [];
				$planet->terrain[] = $array;
				for ($i2 = 0; $i2 < $l; $i2++) {
					$terrain = new PlanetTerrains();
					$terrain->x = $i2;
					$terrain->y = $i;
					$terrain->terrainid = $_POST['planetGrid'][$i][$i2];
					$planet->terrain[$i][$i2] = $terrain;
				}
			}
			$planet->commit();
	}
	else if ($route[1] == 'update') {

	}
	else if ($route[1] == 'delete') {
		$success = true;
		try {
			$planet = Planets::get(intval($route[2]));
			$planet->remove();
		}
		catch (\Exception $e) {
			$success = false;
		}
		return \json_encode([ $success ]);
	}
}
else if ($route[0] == 'terraintypes') {
	echo \json_encode(Terrains::get());
}
