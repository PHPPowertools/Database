<?php
/* !
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *               PACKAGE : PHP POWERTOOLS
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *               COMPONENT : DATABASE 
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * 
 *               DESCRIPTION :
 *
 *               A library for interacting with a database
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * 
 *               REQUIREMENTS :
 *
 *               PHP version 5.4
 *               PSR-0 compatibility
 *
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * 
 *               LICENSE :
 *
 * LICENSE: Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *  @category  Database interaction
 *  @package   Database
 *  @author    John Slegers
 *  @copyright MMXV John Slegers
 *  @license   http://www.opensource.org/licenses/mit-license.html MIT License
 *  @link      https://github.com/jslegers
 * 
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 */

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

    public function distinct($distinct = true) {
        $this->_distinct = $distinct;
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
        $select = $this->_distinct ? 'SELECT DISTINCT' : 'SELECT';
        return $select . $this->_get() . $this->_from() . $this->_where() . $this->_order() . $this->_limit();
    }

}

?>
