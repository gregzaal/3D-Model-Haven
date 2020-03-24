<?php

include ($_SERVER['DOCUMENT_ROOT'].'/php/functions.php');

if(isset($_POST['slug'])){
    $slug = $_POST['slug'];

    $p = join_paths($GLOBALS['SYSTEM_ROOT'], "files", "models", $slug);
    if (file_exists($p)){
        $a = listdir($p);
    }else{
        $a = [];
    }

    $encoded = json_encode($a);
    header('Content-type: application/json');
    exit($encoded);
}

?>
