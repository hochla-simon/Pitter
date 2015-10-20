<?php
$config = array(
	'navigation' => array(
		array('text' => 'Home', 'url' => '/common/home.php'),
		array('text' => 'Upload Photos', 'url' => '/upload/index.php'),
		array('text' => 'View Photos', 'url' => '/view/upload.php')
	)
);

$tmpConfig = json_decode(@file_get_contents(dirname(__FILE__).'/config.txt'), true);
if(count($tmpConfig) == 0){
	$tmpConfig = array(
		'projectName' => 'Pitter',
		'slogan' => 'Manage your pictures privately',
		'copyright' => 'Copyright 2015 by <a href="https://github.com/hochla-simon/Pitter" target="_blank">Pitter</a>'
	);
}

$config = array_merge($config, (array)$tmpConfig);
?>