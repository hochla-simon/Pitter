<?php
require 'FixtureTestCase.php';

class MyTestCase extends FixtureTestCase {

    public $fixtures = array(
        'users'
    );

    function testReadDatabase() {

        $_GET = array(
            'page' => 'administration/settings.php'
        );
        $phpunit = array(
            'isTest' => true
        );
        $config['installed'] = false;
        $_POST = array(
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');

        $config['installed'] = false;
        $_POST = array(
            'databaseHost' => 'localhost',
            'databaseUser' => 'pitter',
            'databasePassword' => 'pitter',
            'databaseName' => 'pitter',
            'projectName' => 'Example2',
            'projectURL' => 'http://www.example.org2',
            'slogan' => 'Example slogan2',
            'copyright' => 'Example copyright2',
            'homeContent' => 'Example home content2',
            'submit' => true
        );
        include(dirname(__FILE__).'/../index.php');

        $this->assertContains('Installation successful.', $message);
        $this->assertEquals($config['installed'], true);

        $this->assertNotNull($db);

        $results = $db->query('SELECT * FROM users');
        $this->assertEquals(mysql_num_rows($results),0);

        $this->setUp();

        $sxe = simplexml_load_file(dirname(__FILE__) . DIRECTORY_SEPARATOR .'fixtures/pitter_database/users.xml');
        $rows = $sxe->xpath('//row');//mysqldump->database->table_data->row;
        //echo $rows->asXML();
        foreach ($rows as $row){
            $firstName = $row->xpath('field[@name=\'firstName\']')[0];
            $lastName = $row->xpath('field[@name=\'lastName\']')[0];
            $email = $row->xpath('field[@name=\'email\']')[0];
            $password = $row->xpath('field[@name=\'password\']')[0];

            $sqlQuery = 'INSERT INTO users (firstName, lastName, email, password) VALUES (\''.$firstName.'\',\''.$lastName.'\',\''.$email.'\',\''.$password.'\');';
            $db->query($sqlQuery);
        }

        $results = $db->query('SELECT * FROM users');
        $this->assertEquals(mysql_num_rows($results),2);



        // now delete them
        $db->query('TRUNCATE users');
        $results = $db->query('SELECT * FROM users');
        $this->assertEquals(mysql_num_rows($results),0);


    }

}