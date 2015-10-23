<?php
require 'FixtureTestCase.php';

class MyTestCase extends FixtureTestCase {

    public $fixtures = array(
        'users'
    );

    function testReadDatabase() {

        $this->setUp();

        $conn = $this->getConnection()->getConnection();

        // fixtures auto loaded, let's read some data
        $query = $conn->query('SELECT * FROM users');
        $results = $query->fetchAll(PDO::FETCH_COLUMN);
        $this->assertEquals(2, count($results));

        // now delete them
        $conn->query('TRUNCATE users');

        $query = $conn->query('SELECT * FROM users');
        $results = $query->fetchAll(PDO::FETCH_COLUMN);
        $this->assertEquals(0, count($results));

        // now reload them
        $ds = $this->getDataSet(array('users'));
        $this->loadDataSet($ds);

        $query = $conn->query('SELECT * FROM users');
        $results = $query->fetchAll(PDO::FETCH_COLUMN);
        $this->assertEquals(2, count($results));
    }

}