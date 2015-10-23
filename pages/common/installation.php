<?php
$site['title'] = 'Database Installation';

if($db->install()){
	echo 'Installed';
}
else{
	echo 'Error: '.$db->getErrorMessage();
}
?>