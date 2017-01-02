<?php

namespace MineManagement;

trait Commitable {
  public $_markForDelete = false;

  public function markForDelete() {
	//If this method is called, this object needs to be marked for deletion.
	$this->_markForDelete = true;
  }

  public function getId() {
	//We need to determine which ID is available and then return the correct value.
	if (isset($this->id)) {
	  return $this->id;
	}
	else if (isset($this->refid)) {
	  return $this->refid;
	}
	else {
	  return null;
	}
  }

  public function commit() {
	//We need to check if getId produced an integer and, if so, we need to process what type of commit is needed.
	$idAvailable = \is_numeric($this->getId());
	//We're checking if the id is available and it's marked for delete. If not, we need to check if the id is available.
	//Finally, we need to make sure it hasn't been marked for delete. if it has and there's no id, nothing needs to be
	//done.
	if ($idAvailable && $this->_markForDelete) {
	  $this->delete();
	}
	else if ($idAvailable) {
	  $this->update();
	}
	else if (!$this->_markForDelete) {
	  $this->insert();
	}
  }
}
