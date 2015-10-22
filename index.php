<?php
error_reporting(E_ALL & ~E_NOTICE);
session_start();
session_regenerate_id();
include(dirname(__FILE__).'/includes/functions.php');
include(dirname(__FILE__).'/includes/config.php');
include(dirname(__FILE__).'/includes/database.php');

if($config['installed']){
	$db = new Database();
	$db->connect();
}

$page = (($_GET['page'] != '') ? $_GET['page'] : 'common/home.php');
/*if(!file_exists(dirname(__FILE__).'/config.txt')){
	$page = 'common/settings.php';
}
else */if(!file_exists(dirname(__FILE__).'/pages/'.$page)){
	$page = 'common/error404.php';
}

$site = array();
ob_start();
include(dirname(__FILE__).'/pages/'.$page);
$site['content'] = ob_get_contents();
ob_end_clean();

include(dirname(__FILE__).'/pages/layout.php');