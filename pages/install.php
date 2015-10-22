<?php
if($db->install()){
	echo 'Installed';
}
else{
	echo 'Error: '.$db->getErrorMessage();
}
?>