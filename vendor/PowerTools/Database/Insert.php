<?php

namespace PowerTools;

class Database_Insert extends Database_Query {

    public $type = 'INSERT';

    public function into($table) {
        return $this->table($table);
    }

    public function __toString() {
        return $this->type . $this->_into() . $this->_values();
    }

}

?>