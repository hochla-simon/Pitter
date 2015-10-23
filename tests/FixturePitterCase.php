<?php
// we're loading the Database TestCase here
require_once dirname( dirname(__FILE__) ).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
#require 'vendor' . DIRECTORY_SEPARATOR . 'Autoload.php';

class FixturePitterCase extends PHPUnit_Extensions_Database_TestCase {

    public $fixtures = array(
        'users'
    );

    private $conn = null;

    public function setUp() {
        echo 'doing setup\n';
        $conn = $this->getConnection();
        $pdo = $conn->getConnection();

        $pdo->exec("DROP TABLE IF EXISTS `tags`;");
        $pdo->exec("DROP TABLE IF EXISTS `metadata`;");
        $pdo->exec("DROP TABLE IF EXISTS `images`;");
        $pdo->exec("DROP TABLE IF EXISTS `users`;");
        $pdo->exec("DROP TABLE IF EXISTS `albums`;");

        $query = file_get_contents(dirname( dirname(__FILE__) ).DIRECTORY_SEPARATOR.'mysql_scripts'.DIRECTORY_SEPARATOR."create_table_v1.sql");

        $stmt = $pdo->prepare($query);

        if ($stmt->execute())
            echo "Success";
        else
            echo "Fail";

        parent::setUp();
    }

    public function tearDown() {

        $conn = $this->getConnection();
        $pdo = $conn->getConnection();

        $pdo->exec("DROP TABLE IF EXISTS `tags`;");
        $pdo->exec("DROP TABLE IF EXISTS `metadata`;");
        $pdo->exec("DROP TABLE IF EXISTS `images`;");
        $pdo->exec("DROP TABLE IF EXISTS `users`;");
        $pdo->exec("DROP TABLE IF EXISTS `albums`;");

        parent::tearDown();
    }

    public function getConnection() {
        if ($this->conn === null) {
            try {
                $pdo = new PDO('mysql:host=localhost;dbname=test', 'root', '');
                $this->conn = $this->createDefaultDBConnection($pdo, 'test');
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
        return $this->conn;
        $pdo = $conn->getConnection();

    }

    public function getDataSet($fixtures = array()) {
        if (empty($fixtures)) {
            $fixtures = $this->fixtures;
        }
        $compositeDs = new
        PHPUnit_Extensions_Database_DataSet_CompositeDataSet(array());
        $fixturePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'pitter_database';

        foreach ($fixtures as $fixture) {
            $path =  $fixturePath . DIRECTORY_SEPARATOR . "$fixture.xml";
            $ds = $this->createMySQLXMLDataSet($path);
            $compositeDs->addDataSet($ds);
        }
        return $compositeDs;
    }

    public function loadDataSet($dataSet) {
        // set the new dataset
        $this->getDatabaseTester()->setDataSet($dataSet);
        // call setUp whateverhich adds the rows
        $this->getDatabaseTester()->onSetUp();
    }
}