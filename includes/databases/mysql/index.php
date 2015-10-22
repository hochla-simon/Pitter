<?php
class Database{
	
	private $connection;
	private $isConnected = false;
	private $msg;
	
	function __construct(){
		
	}
	
	function connectByConfig(){
		global $config;
		if($config['databaseHost'] != '' and $config['databaseUser'] != '' and $config['databasePassword'] != '' and $config['databaseName'] != ''){
			$this->connect($config['databaseHost'], $config['databaseUser'], $config['databasePassword'], $config['databaseName']);
		}
		else{
			$this->setErrorMessage('No Database settings available.');
			return false;
		}
	}

	function connect($host, $user, $password, $database){
		$this->connection = @mysql_connect($host, $user, $password);
		if($this->connection){
			if(!@mysql_select_db($database, $this->connection)){
				$this->setErrorMessage('Dateabase error: Could select the database by using the specified database name.');
				return false;
			}
			else{
				$this->isConnected = true;
			}
		}
		else{
			$this->setErrorMessage('Dateabase error: Could not connect to database by using the specified credentials.');
			return false;
		}
	}
	
	function setErrorMessage($msg){
		$this->msg = $msg;
	}
	
	function query($query){
		return @mysql_query($query, $this->connection);
	}
	
	function testDatabaseConnection(){
		
	}
	
	function isConnected(){
		return $this->isConnected;
	}
}