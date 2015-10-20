<?php
$config = array(
	'navigation' => array(
		array('text' => 'Home', 'url' => '/common/home.html')
	)
);

$openModules = opendir(dirname(__FILE__).'/../pages/');
while($module = readdir($openModules)){
	if($module != '.' and $module != '..' and is_dir(dirname(__FILE__).'/../pages/'.$module) and file_exists(dirname(__FILE__).'/../pages/'.$module.'/config.php')){
		include(dirname(__FILE__).'/../pages/'.$module.'/config.php');
		$config['modules'][$module] = $moduleConfig;
		addModuleNavigation($module, $moduleConfig['navigation']);
	}
}



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