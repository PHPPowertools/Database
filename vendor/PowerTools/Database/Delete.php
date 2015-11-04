<?php

namespace PowerTools;

class Database_Delete extends Database_Query {

    public $type = 'DELETE';

    public function from($table) {
        return $this->table($table);
    }

    public function where($where) {
        $this->where = $where;
        return $this;
    }

    public function limit($limit) {
        $this->_limit = func_get_args();
        return $this;
    }

    public function __toString() {
        return $this->type . $this->_from() . $this->_where() . $this->_limit();
    }

}

?>