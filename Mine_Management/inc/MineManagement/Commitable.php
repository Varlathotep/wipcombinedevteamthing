<?php

namespace MineManagement;

trait Commitable {
  public $_markForDelete = false;

  public function markForDelete() {
	$this->_markForDelete = true;
  }

  public function getId() {
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
	$idAvailable = \is_numeric($this->getId());
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
