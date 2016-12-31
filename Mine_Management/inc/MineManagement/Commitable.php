<?php

namespace MineManagement;

trait Commitable {
	private $_markForDelete = false;

	public function markForDelete() {
		$this->_markForDelete = true;
	}

	public function commit() {
		$idAvailable = \is_numeric($this->id);
		if (!$idAvailable) {
			$idAvailable = \is_numeric($this->refId);
		}
		if ($idAvailable && $this->_markForDelete) {
			$this->delete();
		}
		else if ($idAvailable) {
			$this->update();
		}
		else {
			$this->insert();
		}
	}
}
