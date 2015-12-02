<?php
if(!$phpunit['isTest']) {
    header("HTTP/1.0 404 Not Found");
}
$site['title'] = 'Not Found';
?>
<h2><?=$site['title']?></h2>
The requested page could not be found.