<?php
class Database{
	
	private $connection;
	private $isConnected = false;
	private $msg;

	function connect($host, $user, $password, $database){
		$this->connection = @mysql_connect($host, $user, $password);
		if($this->connection){
			if(!@mysql_select_db($database, $this->connection)){

				$this->msg = 'Database error: '.mysql_error();
				return false;
			}
			else{
				$this->isConnected = true;
				return true;
			}
		}
		else{
			$this->msg = 'Dateabase error: Could not connect to database by using the specified credentials.';
			return false;
		}
	}

	function getErrorMessage(){
		return $this->msg;
	}
	
	function query($query){
		$result = @mysql_query($query, $this->connection);
		if(!$result){
			$this->msg = mysql_error();
		}
		else{
			$this->msg = '';
		}
		return $result;
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