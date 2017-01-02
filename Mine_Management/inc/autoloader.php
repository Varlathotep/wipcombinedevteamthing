<?php
namespace MineManagement; 
	/**
		Handles automatically loading classes when they're found.
		@param	$name	A string containing the name of the class to be loaded.
	 */
function autoloader($name) {
  $baseName = $name . '.php';
  $baseName = \str_replace('\\', '/', $baseName);
  $found = false;
  //Attempting to find the 2 most likely path names of the class being accessed. If neither of these names are found, then we're going to try to "lazily" find the name based off of the given namespace.
  if (\file_exists($baseName)) {
	include_once($baseName);
	$found = true;
  }
  else if (\file_exists('inc/' . $baseName)) {
	include_once('inc/' . $baseName);
	$found = true;
  }
  else {
	$explodedName = \explode('/', $baseName);
	//This is probably somewhat inefficient, but it's going to search for a filename if it can't find one based off of the original 2 criteria.
	while (\count($explodedName) > 0) {
	  \array_shift($explodedName);
	  $workingName = \implode('/', $explodedName);
	  if (\file_exists($workingName)) {
		include_once($workingName);
		$found = true;
		break;
	  }
	  else if (\file_exists('inc/' . $workingName)) {
		include_once($workingName);
		$found = true;
		break;
	  }
	}
  }
  //We need to throw an exception if the autoloader can't find the class.
  if (!$found) {
	throw new \Exception('The class ' . $name . ' could not be found. Please ensure that this class has a file created for it.');
  }
}

\spl_autoload_register('MineManagement\autoloader');

