<?php
if(!$phpunit['isTest']) {
    header("HTTP/1.0 400 Bad Request");
}
$site['title'] = 'Bad Request';
?>
<h2><?=$site['title']?></h2>
Some error happened answering your request.