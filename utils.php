<?php

function mysql_fix_escape_string($text){
    if(is_array($text)){
        return false;
    } 
    str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"),array('', '', '', '', "", '', ''),$text); 
    $text = str_replace("'","",$text);
    $text = str_replace('"',"",$text);
    return $text;
}

?>
