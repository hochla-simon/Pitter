<?php
$config = array(
	'navigation' => array(
		array('text' => 'Home', 'url' => 'common/home.html', 'position' => 0)
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



$tmpConfig = json_decode(@file_get_contents(dirname(__FILE__).'/../data/configuration/config.txt'), true);
if(count($tmpConfig) == 0){
	$tmpConfig = array(
		'databaseType' => 'mysql',
		'projectName' => 'Pitter',
		'slogan' => 'Manage your pictures privately',
		'copyright' => 'Copyright 2015 by <a href="https://github.com/hochla-simon/Pitter" target="_blank">Pitter</a>',
		'homeContent' => '<h2>What is Pitter?</h2>
			<p>Pitter is a private photo gallery where you can store your private photos securely in your own server.
			It is implemented by using the most supported technologies so it is easy to install. Using the software is also very easy.
			Uploading is done by selecting your photos from your device folder or just simply drag and drop them to the uploading area in the upload tab.
			After the upload is finished. You can browse your photos, move them into albums and even create a slide show to watch with other people.
			You can also share your photos with the people you trust.</p>
			<p>Unlike other services where you can store photos, Pitter do not use your photos to other purposes.
			In free services like Google Photos, Dropbox and Facebook, you will never know where is your photos used and what kind of information the service provider is collecting about you.
			Pitter will not send any information or photos to anyone unless you share it with them.</p>
			<p>Pitter is our course project in Advanced Software Engineering course in UPC (Barcelona, Spain) and it is an open source project that is maintained in Github (https://github.com/hochla-simon/Pitter).
			We planned to do this as a open source project because we think people should have a right to store their photos privately without paying for it.
			Also people can see from the source code, how this software will handle their photos.</p>'
	);
}

$config = array_merge($config, (array)$tmpConfig);
