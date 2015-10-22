<?php
class Database{
	
	private $connection;
	private $isConnected = false;
	private $msg;

	function connect($host, $user, $password, $database){
		$this->connection = @mysql_connect($host, $user, $password);
		if($this->connection){
			if(!@mysql_select_db($database, $this->connection)){
				$this->setErrorMessage('Database error: '.mysql_error());
				return false;
			}
			else{
				$this->isConnected = true;
				return true;
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

	function getErrorMessage(){
		return $this->msg;
	}
	
	function query($query){
		return @mysql_query($query, $this->connection);
	}
	
	function multiQuery($query){
		$query = explode(';', $query);
		foreach($query as $part){
			if(trim($part) == ''){
				continue;
			}
			if(!$this->query($part)){
				return false;
			}
		}
		return true;
	}
	
	function isConnected(){
		return $this->isConnected;
	}
	
	function install(){
		$createSQL = file_get_contents(dirname(__FILE__).'/create.sql');
		$cleanSQL = file_get_contents(dirname(__FILE__).'/clean.sql');
		return ($this->multiQuery($cleanSQL) and $this->multiQuery($createSQL));
	}
}