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
