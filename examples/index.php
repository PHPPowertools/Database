<?php

namespace App;

use \PowerTools\Database as Database;

define("LOCAL_PATH_BOOTSTRAP", __DIR__);
require LOCAL_PATH_BOOTSTRAP . DIRECTORY_SEPARATOR . 'bootstrap.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" media="all"  href="<?php echo REQUEST_PROTOCOL . HTTP_PATH_ASSET_CSS; ?>/cascade-full.min.css" />
        <link rel="stylesheet" type="text/css" media="all"  href="<?php echo REQUEST_PROTOCOL . HTTP_PATH_ASSET_CSS; ?>/site.css" />
        <title>PHP PowerTools Boilerplate</title>
        <meta name="description" content="Boilerplate for PHP PowerTools projects">
        <link rel="shortcut icon" href="<?php echo REQUEST_PROTOCOL . HTTP_PATH_ASSET_IMG; ?>/favicon.ico" type="image/x-icon" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <div class="site-header">
            <div class="site-center">
                <div class="cell">
                    <h1>PHP PowerTools Boilerplate</h1>
                </div>
            </div>
        </div>
        <div class="site-body">
            <div class="site-center">
                <div class="cell">
                    <span class="center icon icon-globe icon-64"></span>
                    <p>Some example queries.</p>
                    <pre><?php
                        define("DB", "test");    // The database name.
                        define("DBHOST", "localhost");     // The host you want to connect to.
                        define("DBUSER", "root");    // The database username. 
                        define("DBPASS", "root");    // The database password. 

                        $database = Database::factory(DB, DBHOST, array(DBUSER => DBPASS))->connect();
                        
                        
                        if ($data = $database
                                ->select(['numberofpersons' => ['count' => '*']])
                                ->from('person')
                                ->execute()) {
                            var_dump($data);
                        }
                        

                        if ($data = $database
                                ->select('id', 'firstname', 'name', 'profession')
                                ->from('contact')
                                ->where(['firstname' => 'John', 'name' => 'Slegers'])
                                ->order('firstname')
                                ->limit(0, 10)
                                ->execute(true)) {
                            var_dump($data);
                        }

                        if ($data = $database
                                ->select(['time' => ['now' => []]], ['fullname' => ['concat' => ['firstname', ['string' => ' '], 'name']]], 'profession')
                                ->from('contact')
                                ->where(['firstname' => 'John', 'name' => 'Slegers'])
                                ->order('firstname')
                                ->limit(0, 10)
                                ->execute(true)) {
                            var_dump($data);
                        }


                        if ($data = $database
                                ->select()
                                ->from('user')
                                ->where(['firstname' => ['<>' => 'John']])
                                ->order(['firstname' => 'DESC'])
                                ->limit(0, 10)
                                ->execute(true)) {
                            var_dump($data);
                        }

                        if ($data = $database
                                        ->rightjoin(
                                                $database
                                                ->select('id', 'name', 'firstname', 'profession')
                                                ->from('contact')
                                                ->where(array('firstname' => 'John')), $database
                                                ->select('id', 'username', 'name', 'firstname')
                                                ->from('user')
                                                ->where(array())
                                        )
                                        ->on('firstname', ['name' => 'name'])
                                        ->limit(10)->execute(true)) {
                            var_dump($data);
                        }


                        if ($data = $database
                                        ->select('id', 'name', 'firstname', 'profession')
                                        ->from('contact')
                                        ->where(array('firstname' => 'John'))
                                        ->leftjoin(
                                                $database
                                                ->select('username', 'name', 'firstname')
                                                ->from('user')
                                        )->on('firstname', ['name' => 'name'])
                                        ->leftjoin(
                                                $database
                                                ->select('name', 'firstname', 'profession')
                                                ->from('person')
                                                ->where(array('profession' => 'Developer'))
                                        )->on('firstname', ['name' => 'name'])
                                        ->limit(10)->execute(true)) {
                            var_dump($data);
                        }

                        if ($data = $database
                                        ->select('id', 'name', 'firstname', 'profession')
                                        ->from('contact')
                                        ->where(array('firstname' => 'John'))
                                        ->leftjoin(
                                                $database
                                                ->select('username', 'name', 'firstname')
                                                ->from('user')
                                                ->leftjoin(
                                                        $database
                                                        ->select('name', 'firstname', 'profession')
                                                        ->from('person')
                                                        ->where(array('profession' => 'Developer'))
                                                )->on('firstname', ['name' => 'name'])
                                        )->on('firstname', ['name' => 'name'])
                                        ->limit(10)->execute(true)) {
                            var_dump($data);
                        }

                        if ($data = $database->leftjoin(
                                                $database
                                                ->select('id', 'name', 'firstname', 'profession')
                                                ->from('contact')
                                                ->where(array('firstname' => 'John')), $database
                                                ->select('username', 'name', 'firstname')
                                                ->from('user')
                                                ->leftjoin(
                                                        $database
                                                        ->select('name', 'firstname', 'profession')
                                                        ->from('person')
                                                        ->where(array('profession' => 'Developer'))
                                                )->on('firstname', ['name' => 'name'])
                                        )->on('firstname', ['name' => 'name'])
                                        ->limit(10)->execute(true)) {
                            var_dump($data);
                        }

                        if ($data = $database->leftjoin(
                                                $database
                                                ->select('id', 'name', 'firstname', 'profession')
                                                ->from('contact')
                                                ->where(array('firstname' => 'John'))
                                                ->leftjoin(
                                                        $database
                                                        ->select('username', 'name', 'firstname')
                                                        ->from('user')
                                                )->on('firstname', ['name' => 'name']), $database
                                                ->select('name', 'firstname', 'profession')
                                                ->from('person')
                                                ->where(array('profession' => 'Developer'))
                                        )->on('firstname', ['name' => 'name'])
                                        ->limit(10)->execute(true)) {
                            var_dump($data);
                        }

                        if ($data = $database
                                        ->select('id', 'name', 'firstname', 'profession')
                                        ->from('contact')
                                        ->where(array('firstname' => 'John'))
                                        ->leftjoin(
                                                $database
                                                ->leftjoin(
                                                        $database
                                                        ->select('username', 'name', 'firstname')
                                                        ->from('user'), $database
                                                        ->select('name', 'firstname', 'profession')
                                                        ->from('person')
                                                        ->where(array('profession' => 'Developer'))
                                                )->on('firstname', ['name' => 'name'])
                                        )->on('firstname', ['name' => 'name'])
                                        ->limit(10)->execute(true)) {
                            var_dump($data);
                        }

                        if ($data = $database
                                        ->leftjoin(
                                                $database
                                                ->select('id', 'name', 'firstname', 'profession')
                                                ->from('contact')
                                                ->where(array('firstname' => 'John')), $database
                                                ->leftjoin(
                                                        $database
                                                        ->select('username', 'name', 'firstname')
                                                        ->from('user'), $database
                                                        ->select('name', 'firstname', 'profession')
                                                        ->from('person')
                                                        ->where(array('profession' => 'Developer'))
                                                )->on('firstname', ['name' => 'name'])
                                        )->on('firstname', ['name' => 'name'])
                                        ->limit(10)->execute(true)) {
                            var_dump($data);
                        }

                        if ($data = $database
                                ->insert(['firstname' => 'Ian', 'name' => 'Stevens', 'profession' => 'CEO'])
                                ->into('contact')
                                ->execute()) {
                            var_dump($data);
                        }

                        if ($data = $database
                                ->update(['profession' => 'CTO'])
                                ->in('contact')
                                ->where(['firstname' => 'Ian'])
                                ->order('firstname')
                                ->limit(10)
                                ->execute()) {
                            var_dump($data);
                        }

                        $database->disconnect();
                        ?></pre>
                </div>
            </div>
        </div>
        <div class="site-footer">
            <div class="site-center" id="site-footer-content">
                <div class="col">
                    <div class="cell pipes">
                        <ul class="nav">
                            <li>Powered by <a target="_blank" href="http://www.johnslegers.com">Cascade Framework</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <script src="<?php echo REQUEST_PROTOCOL . HTTP_PATH_ASSET_JS; ?>/app.js"></script>
    </body>
</html>
