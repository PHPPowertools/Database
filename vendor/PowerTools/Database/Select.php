<?php

namespace PowerTools;

class Database_Select extends Database_Query {

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

    public function order($order) {
        $this->_order = func_get_args();
        return $this;
    }

    public function distinct($distinct = true) {
        $this->_distinct = $distinct;
        return $this;
    }

    public function __toString() {
        $select = $this->_distinct ? $this->type . ' DISTINCT' : $this->type;
        return $select . $this->_get() . $this->_from() . $this->_where() . $this->_order() . $this->_limit();
    }

    protected function _build($obj, $args) {
        array_unshift($args, $this);
        return $obj::factory($args)->setDatabase($this->db);
    }

    public function innerjoin() {
        return $this->_build('\PowerTools\Database_Join', func_get_args());
    }

    public function leftjoin() {
        $obj = $this->_build('\PowerTools\Database_Join', func_get_args());
        $obj->type = 'LEFT JOIN';
        return $obj;
    }

    public function rightjoin() {
        $obj = $this->_build('\PowerTools\Database_Join', func_get_args());
        $obj->type = 'RIGHT JOIN';
        return $obj;
    }
}

?>
