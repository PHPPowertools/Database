<?php

namespace PowerTools;

class Database_Join extends Database_Select {

    public $type = 'INNER JOIN';
    protected $_on = '';

    public function limit($limit) {
        $this->_limit = func_get_args();
        return $this;
    }

    public function on($on) {
        $this->_on = func_get_args();
        return $this;
    }

    public function getTablename($id = 0) {
        if ($this->fields[$id] instanceof Database_Join) {
            return $this->fields[$id]->getTablename(0);
        } else {
            return $this->fields[$id]->table;
        }
    }

    public function recurse($function = false) {
        $return = array();
        foreach ($this->fields as $i => $query) {
            if ($query instanceof Database_Join) {
                if ($data = $query->recurse($function)) {
                    $return = array_merge($return, $data);
                }
            } else {
                if ($function) {
                    $data = $function($query, $i);
                } else {
                    $data = $query;
                }
                if ($data) {
                    array_push($return, $data);
                }
            }
        }
        return $return;
    }

    public function joinConditions() {
        $on = array();
        foreach ($this->_on as $condition) {
            if (count($condition) > 0) {
                $firsttable = $this->getTablename(0);
                $secondtable = $this->getTablename(1);
                if (is_array($condition)) {
                    foreach ($condition as $k => $v) {
                        array_push($on, ' ' . $this->addticks($firsttable) . '.' . $this->addticks($k) . ' = ' . $this->addticks($secondtable) . '.' . $this->addticks($v));
                    }
                } else {
                    array_push($on, ' ' . $this->addticks($firsttable) . '.' . $this->addticks($condition) . ' = ' . $this->addticks($secondtable) . '.' . $this->addticks($condition));
                }
            }
        }
        return ' ON' . implode(' AND', $on);
    }

    public function fromList($joinConditions = false) {
        $return = array();
        foreach ($this->fields as $index => $query) {
            if ($query instanceof Database_Join) {
                if ($index === 1) {
                    $data = $query->fromList($this->joinConditions());
                } else {
                    $data = $query->fromList($joinConditions);
                }
            } else {
                if ($index === 1) {
                    $data = ' ' . $query->addticks($query->table) . $this->joinConditions();
                } else {
                    $data = ' ' . $query->addticks($query->table) . ($joinConditions ? $joinConditions : '');
                }
            }
            if ($data) {
                array_push($return, $data);
            }
        }
        return implode(' ' . $this->type, $return);
    }

    public function selectionList() {
        return implode(',', $this->recurse(function($query) {
                    $fieldsraw = $query->fields;
                    if (count($fieldsraw) > 0) {
                        $fields = array();
                        foreach ($fieldsraw as $k => $v) {
                            array_push($fields, ' ' . $this->addticks($query->table) . '.' . $this->addticks($v) . ' AS ' . $this->_adjustValue($query->table . '.' . $v));
                        }
                        return implode(',', $fields);
                    } else {
                        return $this->addticks($query->table) . ' .*';
                    }
                }));
    }

    public function whereList() {
        return implode(' AND', $this->recurse(function($query) {
                    $wheresraw = $query->where;
                    if (count($wheresraw) > 0) {
                        $where = array();
                        foreach ($wheresraw as $k => $v) {
                            array_push($where, ' ' . $this->addticks($query->table) . '.' . $this->_conditions($v, $k));
                        }
                        return implode(' AND', $where);
                    } else {
                        return '';
                    }
                }));
    }

    protected function _get() {
        return $this->selectionList();
    }

    protected function _where() {
        return ' WHERE' . $this->whereList();
    }

    protected function _from() {
        return ' FROM' . $this->fromList();
    }

    public function __toString() {
        return 'SELECT' . $this->_get() . $this->_from() . $this->_where() . $this->_order() . $this->_limit();
    }

}

?>