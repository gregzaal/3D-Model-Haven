<?php

include($_SERVER['DOCUMENT_ROOT'].'/php/secret_config.php');


$WORKING_LOCALLY = substr($_SERVER['DOCUMENT_ROOT'], 0, 3) == "C:/" || substr($_SERVER['DOCUMENT_ROOT'], 0, 3) == "X:/";

$SYSTEM_ROOT = $_SERVER['DOCUMENT_ROOT'];
if ($WORKING_LOCALLY){
    $SYSTEM_ROOT = $GLOBALS['LOCAL_WORKING_FOLDER'];
}

require_once($_SERVER['DOCUMENT_ROOT'].'/vendor/patreon/patreon/src/patreon.php');
use Patreon\API;
use Patreon\OAuth;

$patreon = get_patreon();
$PATRON_LIST = $patreon[0];
$PATREON_GOALS = $patreon[2];
$PATREON_CURRENT_GOAL = null;
foreach ($PATREON_GOALS as $g){
    if ($g['completed_percentage'] < 100){
        $PATREON_CURRENT_GOAL = $g;
        break;
    }
}
$PATREON_EARNINGS = floor(($PATREON_CURRENT_GOAL['amount_cents']*($PATREON_CURRENT_GOAL['completed_percentage']/100))/100);

// Don't cache these pages | GET params ignored | matched to $_SERVER['PHP_SELF']
$NO_CACHE = ["/gallery/do_submit.php",
             "/gallery/moderate.php"
            ];

function nice_name($name, $mode="normal"){
    $str = str_replace('_', ' ', $name);
    if ($mode=="category"){
        // Some categories have a slash in them, but that would ruin URLs so they are stored as a dash instead and then replaced with a slash for display
        $str = implode('/', array_map('ucfirst', explode('-', $str)));
    }
    $str = ucwords($str);
    return $str;
}

function to_slug($name){
    $name = str_replace(' ', '_', $name);
    $name = strtolower($name);
    $name = simple_chars_only($name);
    return $name;
}

function starts_with($haystack, $needle) {
    return substr($haystack, 0, strlen($needle)) === $needle;
}

function ends_with($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

function str_contains($haystack, $needle) {
    return strpos($haystack, $needle) !== false;
}

function str_lreplace($search, $replace, $subject) {
    // Replace only last occurance in string
    $pos = strrpos($subject, $search);

    if($pos !== false)
    {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
}

function random_hash($length=8){
    $chars = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM0123456789";
    $hash = "";
    for ($i=0; $i<$length; $i++){
        $hash .= $chars[rand(0, strlen($chars)-1)];
    }
    return $hash;
}

function simple_hash($str){
    // Simple not-so-secure 8-char hash
    return hash('crc32', $GLOBALS['GEN_HASH_SALT'].$str, FALSE);
}

function simple_chars_only($str){
    return preg_replace("/[^A-Za-z0-9_\- ]/", '', $str);
}

function numbers_only($str){
    return preg_replace("/[^0-9]/", '', $str);
}

function map_range($value, $fromLow, $fromHigh, $toLow, $toHigh) {
    $fromRange = $fromHigh - $fromLow;
    $toRange = $toHigh - $toLow;
    $scaleFactor = $toRange / $fromRange;

    $tmpValue = $value - $fromLow;
    $tmpValue *= $scaleFactor;
    return $tmpValue + $toLow;
}

function time_ago($strtime) {
    // Source: http://goo.gl/LQJWnW

    $time = time() - strtotime($strtime); // to get the time since that moment

    $tokens = array (
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        $rstr = $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'')." ago";
        if ($text == 'day'){
            $rstr = "<span class='new-tex'>".$rstr."</span>";
        }
        return $rstr;
    }
    return "<span class='new-tex'>Today</span>";
}

function is_in_the_past($d) {
    return (time() - strtotime($d) > 0);
}

function first_in_array($a){
    // Return first item of array, php is silly
    $a = array_reverse($a);
    return array_pop($a);
}

function array_sort($array, $on, $order=SORT_ASC){

    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
                break;
            case SORT_DESC:
                arsort($sortable_array);
                break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}

function resize_image($old_fp, $new_fp, $format, $size_x, $size_y, $quality=85){
    $img = new imagick($old_fp);
    $img->resizeImage($size_x, $size_y, imagick::FILTER_BOX, 1, true);
    $img->setImageFormat($format);
    if ($format == "jpg"){
        $img->setImageCompression(Imagick::COMPRESSION_JPEG);
        $img->setImageCompressionQuality($quality);
    }
    $img->writeImage($new_fp);
}

function clean_email_string($string) {
    $bad = array("content-type","bcc:","to:","cc:","<script>");
    return str_replace($bad,"",$string);
}

function debug_email($subject, $text){
    $email_to = $GLOBALS['ADMIN_EMAIL'];
    $email_from = "info@hdrihaven.com";
    $headers = 'From: '.$email_from."\r\n".
    'Reply-To: '.$email_from."\r\n" .
    'MIME-Version: 1.0' . "\r\n" .
    'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
    @mail($email_to, "texhav__".$subject, clean_email_string($text), $headers);
}

function debug_console($str){
    echo "<script>";
    echo "console.log(\"".$str."\");";
    echo "</script>";
}

function print_ra($array){
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}

function join_paths() {
    $paths = array();
    foreach (func_get_args() as $arg) {
        if ($arg !== '') { $paths[] = $arg; }
    }
    return preg_replace('#/+#','/',join('/', $paths));
}

function listdir($d, $mode="ALL"){
    // List contents of folder, without hidden files
    $sd = scandir($d);
    $files = [];
    foreach ($sd as $f){
        if (!starts_with($f, '.')){
            $is_file = str_contains($f, '.');  // is_dir doesn't work reliably on windows, so we assume all folders do not contain '.' #YOLO
            if (($mode == "ALL") or ($mode == "FOLDERS" and !$is_file) or ($mode == "FILES" and $is_file)){
                array_push($files, $f);
            }
        }
    }
    return $files;
}

function qmkdir($d) {
    // Quitly mkdir if it doesn't exist aleady, recursively
    if (!file_exists($d)){
        mkdir($d, 0777, true);
    }
}

function clear_cache(){
    $cache_dir = $_SERVER['DOCUMENT_ROOT']."/php/cache/";
    $r = array_map('unlink', glob("$cache_dir*.html"));
    return sizeof($r);  // Number of cache files cleared
}


?>
