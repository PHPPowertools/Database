<?php

namespace PowerTools;

class Database_Query {

    public $fields = array();
    public $where = array();
    public $table = '';
    public $type = 'SELECT';
    protected $_limit = array();
    protected $_order = array();
    public $db = null;

    public static function factory($args = array()) {
        return new static($args);
    }

    public function __construct($args = array()) {
        $this->fields = $args;
    }

    public function table($table) {
        $this->table = $table;
        return $this;
    }

    public function execute($fetchAll = true) {
        return $this->db->query((string) $this, $fetchAll);
    }

    public function _adjustValue($value) {
        if (is_string($value)) {
            return '"' . $value . '"';
        } elseif ($value === NULL) {
            return 'NULL';
        }
        return $value;
    }

    public function addticks($value) {
        if($value === '*') {
            return $value;
        } else {
            return '`' . $value . '`';
        }
    }

    protected function _condition($right, $left, $comp = '=') {
        if ($right === NULL) {
            $comp = 'IS';
        }
        return $this->addticks($left) . ' ' . $comp . ' ' . $this->_adjustValue($right);
    }

    protected function _ordering($order) {
        if (is_array($order)) {
            foreach ($order as $ordervalue => $ordertype) {
                return $this->addticks($ordervalue) . ' ' . strtoupper($ordertype);
            }
        } else {
            return $this->addticks($order) . ' ASC';
        }
    }

    protected function _function($right, $left) {
        $left = strtoupper($left);
        if ($left === 'STRING') {
            return $this->_adjustValue($right);
        } elseif (is_array($right)) {
            if (count($right) > 0) {
                $return = array();
                foreach ($right as $rightkey => $rightvalue) {
                    if (is_array($rightvalue)) {
                        array_push($return, $this->_process('', $rightvalue, '_function', ','));
                    } else {
                        array_push($return, $this->addticks($rightvalue));
                    }
                }
                return $left . '( ' . implode(', ', $return) . ' )';
            } else {
                return $left . '()';
            }
        } else {
            return $left . '( ' . $this->addticks($right) . ' )';
        }
    }

    protected function _selection($selection, $left) {
        if (is_array($selection)) {
            foreach ($selection as $selectionkey => $selectionvalue) {
                return $this->_process('', $selectionvalue, '_function', ', ') . ' AS ' . $this->_adjustValue($selectionkey);
            }
        } else {
            return $this->addticks($selection);
        }
    }

    protected function _conditions($right, $left) {
        if (is_array($right)) {
            $return = array();
            foreach ($right as $rightkey => $rightvalue) {
                array_push($return, $this->_condition($rightvalue, $left, $rightkey));
            }
            return implode('AND', $return);
        } else {
            return $this->_condition($right, $left, '=');
        }
    }

    protected function _table() {
        return ' ' . $this->addticks($this->table);
    }

    protected function _into() {
        return ' INTO' . $this->_table();
    }

    protected function _from() {
        return ' FROM' . $this->_table();
    }

    protected function _process($prefix = ' ', $data, $process = '_adjustValue', $separator = ', ') {
        $return = array();
        if ($prefix !== ' ' && $prefix !== '') {
            $prefix = ' ' . $prefix;
        }
        if ($separator) {
            foreach ($data as $k => $v) {
                array_push($return, $this->$process($v, $k));
            }
            return $prefix . implode($separator, $return);
        } else {
            foreach ($data as $k => $v) {
                $return[$this->addticks($k)] = $this->$process($v, $k);
            }
            $keys = ' ( ' . implode(', ', array_keys($return)) . ' )';
            $fields = ' ( ' . implode(', ', $return) . ' )';
            return $keys . $prefix . $fields;
        }
    }

    protected function _get() {
        if (count($this->fields) > 0) {
            return $this->_process(' ', $this->fields, '_selection');
        } else {
            return ' *';
        }
    }

    protected function _set() {
        if (count($this->fields) > 0) {
            return $this->_process('SET ', $this->fields, '_conditions', ',');
        } else {
            return '';
        }
    }

    protected function _where() {
        if (count($this->where) > 0) {
            return $this->_process('WHERE ', $this->where, '_conditions', ' AND');
        } else {
            return '';
        }
    }

    protected function _limit() {
        if (count($this->_limit) > 0) {
            return $this->_process('LIMIT ', $this->_limit);
        } else {
            return '';
        }
    }

    protected function _order() {
        if (count($this->_order) > 0) {
            return $this->_process('ORDER BY ', $this->_order, '_ordering');
        } else {
            return '';
        }
    }

    protected function _values() {
        if (count($this->fields) > 0) {
            return $this->_process('VALUES ', $this->fields, '_adjustValue', false);
        } else {
            return '';
        }
    }

    public function setDatabase($db) {
        $this->db = $db;
        return $this;
    }

}
