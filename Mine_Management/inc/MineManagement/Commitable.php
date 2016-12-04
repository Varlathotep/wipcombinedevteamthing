<?php

namespace MineManagement;
trait Commitable {
	public function commit() {
		if (is_numeric($this->id)) {
			$this->update();
		}
		else {
			$this->insert();
		}
	}
}
