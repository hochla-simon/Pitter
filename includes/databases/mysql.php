<?php
if($config['databaseHost'] != '' and $config['databaseUser'] != '' and $config['databasePassword'] != '' and $config['databaseName'] != ''){
	$dbConnection = mysql_connect($config['databaseHost'], $config['databaseUser'], $config['databasePassword']);
	if($dbConnection){
		if(!mysql_select_db($config['databaseName'])){
			createMessage('Dateabase error: Could select the database by using the specified database name.');
		}
	}
	else{
		createMessage('Dateabase error: Could not connect to database by using the specified credentials.');
	}
}