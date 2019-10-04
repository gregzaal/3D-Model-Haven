<?php

include ($_SERVER['DOCUMENT_ROOT'].'/php/functions.php');
include($_SERVER['DOCUMENT_ROOT'].'/php/html/cache_top.php');

$models = get_from_db();
$json = array();
foreach ($models as $t){
    $tags = $t['tags'].";".$t['categories'];
    $json[$t['slug']] = $tags;
}

echo json_encode($json, JSON_PRETTY_PRINT);

include($_SERVER['DOCUMENT_ROOT'].'/php/html/cache_bottom.php');

?>
