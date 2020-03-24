<?php

include ($_SERVER['DOCUMENT_ROOT'].'/php/functions.php');

if(isset($_POST['slug'])){
    $slug = $_POST['slug'];

    $a = listdir(join_paths($GLOBALS['SYSTEM_ROOT'], "files", "models", $slug));

    $encoded = json_encode($a);
    header('Content-type: application/json');
    exit($encoded);
}

?>
