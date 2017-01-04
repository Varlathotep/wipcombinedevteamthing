<?php
namespace MineManagement;
error_reporting(E_ALL & ~E_DEPRECATED);
include('inc/autoloader.php');
$route = [];
//We need to explode the route. We process this later.
if (\array_key_exists('q', $_GET)) {
  $route = \explode('/', \trim($_GET['q'], "/\t\n\r\0\x0B"));
}
//We need to start up the database connection. This creates a singleton stored in the database class that all other classes will use.
Database::createConnection('localhost', 'root', 'password', 'minemanagement2');
//If only 1 route value exists, we need to go ahead and load the index.
if (\count($route) === 0) {
  require_once('page/index.html');
}
else if ($route[0] == 'material') {
  if ($route[1] == 'save') {
	$planet = Planets::get($_POST['planetId']);
	for ($i = 0; $i < \count($_POST['materialGrid']); $i++) {
	  for ($i2 = 0; $i2 < \count($_POST['materialGrid'][$i]); $i2++) {
		$matId = $_POST['materialGrid'][$i][$i2];
		$quantity = $_POST['quantity'][$i][$i2];
		$planet->updateDeposit($matId, $quantity, $i2, $i);
	  }
	}
	$planet->commit();
  }
}
else if ($route[0] == 'planets') {
  //We need to determine if a return, add or removal is being called and handle the call correctly.
  if ($route[1] == 'return') {
	//If the second part of the route is equal to all, we need to return all planets available. Otherwise, we need to return only that
	//single ID being provided.
	if ($route[2] == 'all') {
	  echo \json_encode(Planets::get());
	}
	else if (\is_numeric($route[2])) {
	  echo \json_encode(Planets::get($route[2]));
	}
  }
  else if ($route[1] == 'add' && \count($_POST) > 0 && \array_key_exists('planetEditor', $_POST)) {
	//We need to grab the edited/added planet and set its parameters. We then need to modify the terrain as need be and commit it.
	if (!empty($_POST['planetid'])) {
	  $workingPlanet = Planets::get($_POST['planetid']);
	}
	else {
	  $workingPlanet = new Planets();
	  $workingPlanet->width = $workingPlanet->height = (int)$_POST['size'];
	}
	$workingPlanet->name = $_POST['name'];
	//We need to iterate over each terrain chunk and appropriately put it into the object as needed.
	for ($i = 0; $i < $workingPlanet->height || $i < $_POST['size']; $i++) {
	  for ($i2 = 0; $i2 < $workingPlanet->width || $i2 < $_POST['size']; $i2++) {
		if ($i2 > ($_POST['size'] - 1) || $i > ($_POST['size'] - 1)) {
		  $workingPlanet->deleteTerrain($i2, $i);
		}
		else {
		  $workingPlanet->updateTerrain($_POST['planetGrid'][$i][$i2], $i2, $i);
		}
	  }
	}
	//We need to set the width and height to the provided size and then commit it to the database..
	$workingPlanet->width = $workingPlanet->height = (int)$_POST['size'];
	$workingPlanet->commit();
	redirectToHost();
  }
  else if ($route[1] == 'delete') {
	$success = true;
	//We need to determine which type of delete is happening. If an exception is thrown,
	//we know the delete failed. If not, it should have processed correctly.
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
		$planet = Planets::get($route[2]);
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
else if ($route[0] == 'materialtypes') {
  echo \json_encode(Materials::get());
}
function redirectToHost() {
  \header('Location: ' . $_SERVER['HTTP_REFERER']);
}
