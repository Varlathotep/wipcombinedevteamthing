<?php

namespace MineManagement;

interface Stored {
  function update();
  function insert();
  function delete();
  static function select($id = null);
}
