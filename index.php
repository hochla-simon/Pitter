<?php
error_reporting(E_ALL & ~E_NOTICE);

if(!$phpunit['isTest']) {
	session_start();
	session_set_cookie_params(3600);
	session_regenerate_id();
}

include_once(dirname(__FILE__).'/includes/functions.php');
include(dirname(__FILE__).'/includes/config.php');

$page = (($_GET['page'] != '') ? $_GET['page'] : 'common/home.php');
if(!$config['installed']){
	$config['navigation'] = array();
	$page = 'administration/settings.php';
}
else if(!file_exists(dirname(__FILE__).'/pages/'.$page)){
	$page = 'common/error404.php';
}

$site = array();
ob_start();
include(dirname(__FILE__).'/pages/'.$page);
$site['content'] = ob_get_contents();
ob_end_clean();
if(!$phpunit['isTest']){
	include(dirname(__FILE__).'/pages/layout.php');
}
