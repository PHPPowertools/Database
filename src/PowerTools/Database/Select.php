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
