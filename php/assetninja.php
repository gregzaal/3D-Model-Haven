<?php

include ($_SERVER['DOCUMENT_ROOT'].'/php/functions.php');
include($_SERVER['DOCUMENT_ROOT'].'/php/html/cache_top.php');

function scan_dir_for_files_rec($dir, &$results = array())
{
    $files = scandir($dir);
    foreach ($files as $entry) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $entry);
        if (!is_dir($path)) {
            $results[] = $path;
        } else if ($entry != "." && $entry != "..") {
            scan_dir_for_files_rec($path, $results);
        }
    }
    return $results;
}

$latest_version = "1.0";
$available_versions = [
    "1.0",
];
$version = $latest_version;
if (isset($_GET["v"]) && trim($_GET["v"])) {
    $v = $_GET["v"];
    if (in_array($v, $available_versions)) {
        $version = $v;
    } else {
        http_response_code(404);
        echo "Version {$v} not found. Available versions: ";
        echo json_encode($available_versions, JSON_PRETTY_PRINT);
        die();
    }
}

$records = get_from_db();
$json = array();

$json['version'] = $version;
$json['latest_version'] = $latest_version;
$json['last_updated'] = time();

$assets = array();
foreach ($records as $record) {
    $slug = $record['slug'];
    $asset = array();
    $asset['author'] = $record['author'];
    $asset['date_published'] = strtotime($record['date_published']);
    $asset['license'] = "CC0";

    $tags = $record['tags'] . ";" . $record['categories'];
    $tags = explode(';', $tags);
    $asset['tags'] = $tags;

    $file_urls = [];
    $asset_path = join_paths($GLOBALS['SYSTEM_ROOT'], "files", "models", $slug);
    if (file_exists($asset_path)) {
        $file_paths = scan_dir_for_files_rec($asset_path);
        foreach ($file_paths as $file_path) {
            $file_url = "https://3dmodelhaven.com/files/models/{$slug}/" . substr($file_path, strlen($asset_path) + 1);
            array_push($file_urls, $file_url);
        }
    }
    if (count($file_urls) > 0) {
        $asset['files'] = $file_urls;
        $assets[$slug] = $asset;
    }
}
$json['assets'] = $assets;

echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

include($_SERVER['DOCUMENT_ROOT'].'/php/html/cache_bottom.php');

?>