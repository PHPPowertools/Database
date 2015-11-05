<?php

namespace PowerTools;

class Database {

    protected $_query;
    public $_database;

    public static function factory() {
        return new static();
    }

    public function connect($databasename, $hostname, $username, $password) {
        $this->_database = new \mysqli($hostname, $username, $password, $databasename);
        $this->_database->set_charset("utf8");
        return $this;
    }

    public function fetch($result, $fetchAll = true) {
        if ($fetchAll) {
            $data = array();
            for (; $row = $this->fetch($result, false);) {
                array_push($data, $row);
            }
            return $data;
        }
        return $result->fetch_array(MYSQLI_ASSOC);
    }

    public function query($querystring, $fetchAll = true) {
        $result = $this->_database->query((string) $querystring);
        if (!$result || !method_exists($result, 'fetch_array')) {
            return $result;
        } else {
            $return = $this->fetch($result, $fetchAll);
            $result->free();
            return $return;
        }
    }

    protected function _build($obj, $args) {
        return $obj::factory($args)->setDatabase($this);
    }

    public function select() {
        return $this->_build('\PowerTools\Database_Select', func_get_args());
    }

    public function insert($args) {
        return $this->_build('\PowerTools\Database_Insert', $args);
    }

    public function update($args) {
        return $this->_build('\PowerTools\Database_Update', $args);
    }

    public function delete() {
        return $this->_build('\PowerTools\Database_Delete', func_get_args());
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

    public function disconnect() {
        $this->_database->close();
        return $this;
    }

}
