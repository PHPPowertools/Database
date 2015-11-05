# Database Component

***[PHPPowertools](https://github.com/PHPPowertools)*** is a web application framework for PHP > 5.3.

***[PHPPowertools/Database](https://github.com/PHPPowertools/DOM-Query)*** is the second component of ***PHPPowertools*** that has been released to the public.

The purpose of this component is to provide a simple, lightweight, intuitive, chainable interface for interacting with a database.

-----

##### Basic queries :

```php
// Connect with your database
$database = Database::factory()->connect(DB, DBHOST, DBUSER, DBPASS);

// Select all rows from the `contact` table
if ($data = $database->select()->from('contact')->execute()) {
    var_dump($data);
}

// Select all rows from the `user` table & sort by `firstname`
if ($data = $database->select()->from('user')->order('firstname')->execute()) {
    var_dump($data);
}

// Get the number of records found in the `person` table
if ($data = $database
                  ->select(['numberofpersons' => ['count' => '*']])
                  ->from('person')
                  ->execute()) {
    var_dump($data);
}

// Get the first 10 users that do not have `John` as a first name
// and sort them by `firstname` descendingly.
if ($data = $database
                 ->select()
                 ->from('user')
                 ->where(['firstname' => ['<>' => 'John']])
                 ->order(['firstname' => 'DESC'])
                 ->limit(0, 10)
                 ->execute()) {
    var_dump($data);
}

// Insert a new record into the `contact` table
if ($data = $database
                 ->insert(['firstname' => 'Ian', 'name' => 'Stevens', 'profession' => 'CEO'])
                 ->into('contact')
                 ->execute()) {
    var_dump($data);
}

// Update a record in the `contact` table
if ($data = $database
                 ->update(['profession' => 'CTO'])
                 ->in('contact')
                 ->where(['firstname' => 'Ian'])
                 ->order('firstname')
                 ->limit(10)
                 ->execute()) {
     var_dump($data);
}

// Disconnect from your database
$database->disconnect();
```

##### Joining queries :
```php
if ($data = $database
                ->rightjoin(
                     $database
                         ->select('id', 'name', 'firstname', 'profession')
                         ->from('contact')
                         ->where(array('firstname' => 'John')),
                     $database
                         ->select('id', 'username', 'name', 'firstname')
                         ->from('user')
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
                )
                ->on('firstname', ['name' => 'name'])
                ->leftjoin(
                     $database
                         ->select('name', 'firstname', 'profession')
                         ->from('person')
                         ->where(array('profession' => 'Developer'))
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
                         ->leftjoin(
                              $database
                                  ->select('name', 'firstname', 'profession')
                                  ->from('person')
                                  ->where(array('profession' => 'Developer'))
                     )
                     ->on('firstname', ['name' => 'name'])
                )
                ->on('firstname', ['name' => 'name'])
                ->limit(10)->execute(true)) {
    var_dump($data);
}

if ($data = $database
                ->leftjoin(
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
                        )
                        ->on('firstname', ['name' => 'name'])
                )
                ->on('firstname', ['name' => 'name'])
                ->limit(10)->execute(true)) {
    var_dump($data);
}

if ($data = $database
                ->leftjoin(
                     $database
                         ->select('id', 'name', 'firstname', 'profession')
                         ->from('contact')
                         ->where(array('firstname' => 'John'))
                         ->leftjoin(
                              $database
                                  ->select('username', 'name', 'firstname')
                                  ->from('user')
                         )
                         ->on('firstname', ['name' => 'name']),
                     $database
                         ->select('name', 'firstname', 'profession')
                         ->from('person')
                         ->where(array('profession' => 'Developer'))
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
                         ->leftjoin(
                              $database
                                  ->select('username', 'name', 'firstname')
                                  ->from('user'), $database
                                  ->select('name', 'firstname', 'profession')
                                  ->from('person')
                                  ->where(array('profession' => 'Developer'))
                         )
                         ->on('firstname', ['name' => 'name'])
                )
                ->on('firstname', ['name' => 'name'])
                ->limit(10)->execute(true)) {
    var_dump($data);
}

if ($data = $database
                ->leftjoin(
                     $database
                         ->select('id', 'name', 'firstname', 'profession')
                         ->from('contact')
                         ->where(array('firstname' => 'John')),
                     $database
                        ->leftjoin(
                             $database
                                 ->select('username', 'name', 'firstname')
                                ->from('user'), $database
                                ->select('name', 'firstname', 'profession')
                                ->from('person')
                                ->where(array('profession' => 'Developer'))
                        )
                        ->on('firstname', ['name' => 'name'])
                )
                ->on('firstname', ['name' => 'name'])
                ->limit(10)->execute(true)) {
    var_dump($data);
}

```

-----

##### Author

| [![twitter/johnslegers](https://en.gravatar.com/avatar/bf4cc94221382810233575862875e687?s=70)](http://twitter.com/johnslegers "Follow @johnslegers on Twitter") |
|---|
| [John slegers](http://www.johnslegers.com/) |
