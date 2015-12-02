<?php
if(!$phpunit['isTest']) {
    header("HTTP/1.0 401 Unauthorized");
}
$site['title'] = 'Unauthorized';
?>
<h2><?=$site['title']?></h2>
You are not allowed to use this page without authorization.