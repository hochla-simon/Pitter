<?php
if(!$phpunit['isTest']) {
    header("HTTP/1.0 403 Forbidden");
}
$site['title'] = 'Forbidden';
?>
<h2><?=$site['title']?></h2>
You are not allowed to see this page.