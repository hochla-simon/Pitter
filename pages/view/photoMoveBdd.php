<?php
    echo "toto" . safe($_POST['album']) . safe($_POST['photo']) . "toto"  ;



function safe($string)
{
    $string = substr($string, strpos($string, '_')+1);
    return $string;
}