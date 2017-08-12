<?php


function clog($msg,$prefix){
    if (!file_exists('logs')) {
        mkdir('logs', 0777, true);
    }
    $msg = "\n".$msg;
    $date = date('d-M-Y');
    $file = 'logs/'.$prefix.'_'.$date.'.txt';
    file_put_contents($file, $msg, FILE_APPEND);
    echo $msg;
}


?>