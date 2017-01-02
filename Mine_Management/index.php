<?php
namespace MineManagement;
error_reporting(E_ALL & ~E_DEPRECATED);
include('inc/autoloader.php');
$route = [];
//Preparing the route for construction.
if (\array_key_exists('q', $_GET)) {
  $route = \explode('/', \trim($_GET['q'], "/\t\n\r\0\x0B"));
}
//We're going to set up the database connection and then we're going to go to town.
Database::createConnection('localhost', 'root', 'password', 'minemanagement2');
if (\count($route) === 0) {
  require_once('page/index.html');
}
else if ($route[0] == 'planets') {
  if ($route[1] == 'return') {
	if ($route[2] == 'all') {
	  echo \json_encode(Planets::get());
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
	  for ($i2 = 0; $i2 < $l; $i2++) {
		$planet->updateTerrain($_POST['planetGrid'][$i][$i2], $i2, $i);
	  }
	}
	$planet->commit();
  }
  else if ($route[1] == 'add' && \count($_POST) > 0 && \array_key_exists('planetEditor', $_POST) && !empty($_POST['planetid'])) {
	$workingPlanet = Planets::get($_POST['planetid']);
	if ($workingPlanet->width > $_POST['size']) {
	  for ($i = 0; $i < $workingPlanet->height; $i++) {
		for ($i2 = 0; $i2 < $workingPlanet->width; $i2++) {
		  if ($i2 > ($_POST['size'] - 1) || $i > ($_POST['size'] - 1)) {
			$workingPlanet->deleteTerrain($i2, $i);
		  }
		  else {
			$workingPlanet->updateTerrain($_POST['planetGrid'][$i][$i2], $i2, $i);
		  }
		}
	  }
	}
	else if ($workingPlanet->width < $_POST['size']) {
	  for ($i = 0; $i < $_POST['size']; $i++) {
		for ($i2 = 0; $i2 < $_POST['size']; $i2++) {
		  $workingPlanet->updateTerrain($_POST['planetGrid'][$i][$i2], $i2, $i);
		}
	  }
	}
	$workingPlanet->width = $workingPlanet->height = (int)$_POST['size'];
	$workingPlanet->commit();
	var_dump(Database::getConnection());
  }
  else if ($route[1] == 'delete') {
	$success = true;
	if ($route[2] == 'all') {
	  try {
		$planets = Planets::get();
		foreach ($planets as $planet) {
		  $planet->markForDelete();
		  $planet->commit();
		}
	  }
	  catch (\Exception $e) {
		$success = false;
	  }
	}
	else {
	  try {
		$planet = Planets::get(\intval($route[2]));
		$planet->markForDelete();
		$planet->commit();
	  }
	  catch (\Exception $e) {
		$success = false;
	  }
	}
	echo \json_encode([ $success ]);
  }
}
else if ($route[0] == 'terraintypes') {
  echo \json_encode(Terrains::get());
}
