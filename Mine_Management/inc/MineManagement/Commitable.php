<?php

namespace MineManagement;
trait Commitable {
	public function commit() {
		if (isset($this->id) || isset($this->refid)) {
			$this->update();
		}
		else {
			$this->insert();
		}
	}
}
