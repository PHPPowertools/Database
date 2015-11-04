<?php

namespace PowerTools;

class Database_Update extends Database_Query {

    public $type = 'UPDATE';

    public function in($table) {
        return $this->table($table);
    }

    public function where($where) {
        $this->where = $where;
        return $this;
    }

    public function order($limit) {
        $this->_order = func_get_args();
        return $this;
    }

    public function limit($limit) {
        $this->_limit = func_get_args();
        return $this;
    }

    public function __toString() {
        return $this->type . $this->_table() . $this->_set() . $this->_where() . $this->_order() . $this->_limit();
    }

}

?>