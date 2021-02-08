<?php

// Error reporting
// ini_set('display_startup_errors', 1);
// ini_set('display_errors', 1);
// error_reporting(-1);

// Site Variables
$SITE_NAME = "3D Model Haven";
$SITE_DESCRIPTION = "100% Free High Quality 3D Models for Everyone";
$SITE_TAGS = "3D,Model,Arch-viz,Game,Unreal,Unity,Blender,Maya,Max,FBX,Textured,PBR,free,cc0,creative commons";
$SITE_DOMAIN = "3dmodelhaven.com";
$SITE_URL = "https://".$SITE_DOMAIN;
$SITE_LOGO = "/core/img/Model Haven Logo.svg";
$SITE_LOGO_URL = $SITE_URL.$SITE_LOGO;
$META_URL_BASE = $SITE_URL."/files/mod_images/renders/";
$DEFAULT_AUTHOR = "Cameron Casey, Greg Zaal";
$CONTENT_TYPE = "models";  // For DB table name & library url
$CONTENT_TYPE_SHORT = "mod";  // For CSS classes
$CONTENT_TYPE_NAME = "3D models";  // For display
$TEX1_CONTENT_TYPE = "model";
$TEX1_CONTENT_METHOD = "various";
$HANDLE_PATREON = "3dmodelhaven";
$HANDLE_TWITTER = "3DModelHaven";
$HANDLE_FB = "3dmodelhaven";

require_once($_SERVER['DOCUMENT_ROOT'].'/core/core.php');


// ============================================================================
// Database functions
// ============================================================================

function make_sort_SQL($sort) {
    // Return the ORDER BY part of an SQL query based on the sort method
    $sql = "ORDER BY id DESC";
    switch ($sort) {
        case "date_published":
            $sql = "ORDER BY date_published DESC, download_count DESC, slug ASC";
            break;
        case "popular":
            $sql = "ORDER BY download_count/POWER(ABS(DATEDIFF(date_published, NOW()))+1, 1.7) DESC, download_count DESC, slug ASC";
            break;
        case "downloads":
            $sql = "ORDER BY download_count DESC, date_published DESC, slug ASC";
            break;
        default:
            $sql = "ORDER BY id DESC";
    }
    return $sql;
}

function make_search_SQL($search, $category="all", $author="all") {
    // Return the WHERE part of an SQL query based on the search

    $only_past = "date_published <= NOW()";
    $sql = "WHERE ".$only_past;

    if ($search != "all"){
        // Match multiple words using AND
        $terms = explode(" ", $search);
        $i = 0;
        $terms_sql = "";
        foreach ($terms as $t){
            $i++;
            $terms_sql .= " AND ";
            $terms_sql .= "(";
            $terms_sql .= "CONCAT(';',tags,';') REGEXP '[; ]".$t."[; ]'";
            $terms_sql .= " OR ";
            $terms_sql .= "CONCAT(';',categories,';') REGEXP '[; ]".$t."[; ]'";
            $terms_sql .= " OR ";
            $terms_sql .= "name LIKE '%".$t."%'";
            $terms_sql .= ")";
        }
        $sql .= $terms_sql;
    }

    if ($category != "all"){
        $sql .= " AND (categories LIKE '%".$category."%')";
    }

    if ($author != "all"){
        $sql .= " AND (author LIKE '".$author."')";
    }

    return $sql;
}


// ============================================================================
// Model Grid
// ============================================================================

function make_grid_item($i, $category="all"){
    $html = "";

    $slug = $i['slug'];
    $html .= "<a href=\"/model/?";
    if ($category != "all"){
        $html .= "c=".$category."&amp;";
    }
    $html .= "m=".$slug;
    $html .= "\">";
    $html .= "<div class='grid-item'>";

    $html .= "<div class='thumbnail-wrapper'>";

    // Encoded tiny proxy images so that there is *something* to look at while the images load
    $html .= "<img ";
    $html .= "class='thumbnail-proxy' ";
    $local_file = get_slug_thumbnail($slug, 48, 40);
    $proxy_data = base64_encode(file_get_contents($local_file));
    $html .= "src=\"data:image/jpeg;base64,".$proxy_data."\" ";
    $html .= "/>";

    // Main thumbnail images that are only loaded when they come into view
    $html .= "<img ";
    $html .= "class='thumbnail' ";
    $local_file = join_paths($GLOBALS['SYSTEM_ROOT'], "files", "mod_images", "thumbnails", '_dummy_48.png');
    $dummy_data = base64_encode(file_get_contents($local_file));
    $html .= "src=\"data:image/png;base64,".$dummy_data."\" ";
    $img = get_slug_thumbnail($slug, 370, 90);
    $img = filepath_to_url($img);
    $html .= "data-src=\"{$img}\" ";
    $html .= "alt=\"Model: {$i['name']}\" ";
    $html .= "/>";

    $age = time() - strtotime($i['date_published']);
    if ($age < 7*86400){
        // Show "New!" in right corner if item is less than 7 days old
        $html .= '<div class="new-triangle"></div>';
        $html .= '<div class="new">New!</div>';
    }

    $html .= "</div>";  //.thumbnail-wrapper

    $html .= "<div class='description-wrapper'>";
    $html .= "<div class='description'>";

    $html .= "<div class='title-line'>";
    $html .= "<h3>".$i['name']."</h3>";
    $html .= "</div>";

    $html .= "<p class='age'>";
    $html .= time_ago($i['date_published']);
    $html .= " &sdot; ";
    $html .= $i['author'];
    $html .= "</p>";

    $html .= "</div>";  // description

    $html .= "</div>";  // description-wrapper

    $html .= "</div>";  // grid-item
    $html .= "</a>";

    return $html;
}


// ============================================================================
// Model Page
// ============================================================================

function fill_file_table($info, $folder, $parents=[]){
    $slug = $info['slug'];
    $id = random_hash(4);
    $level = sizeof($parents);
    $parent = $level > 0 ? end($parents) : null;

    $files = [];
    foreach (listdir($folder) as $f){
        $files[$f] = join_paths($folder, $f);
    }

    $ignore_start = [".", "_"];
    $ignore_end = [".ini", ".db", ".files"];
    foreach ($files as $f=>$fp){
        foreach ($ignore_start as $e){
            if (starts_with($f, $e)){
                unset($files[$f]);
                break;
            }
        }
        foreach ($ignore_end as $e){
            if (ends_with($f, $e)){
                unset($files[$f]);
                break;
            }
        }
    }
    foreach ($files as $f=>$fp){
        $is_dir = is_dir($fp);
        $dl_url = filepath_to_url($fp);

        // TEST Load Balancer
        if (isset($_GET["testdl"])){
            $dl_url = str_replace("/files/models", "https://download.polyhaven.com/Models", $dl_url);
        }
        // END TEST

        echo $is_dir ? "" : "<a href=\"{$dl_url}\" download=\"{$f}\" target='_blank'>";
        if ($is_dir){
            echo "<div class='folder' id='f_{$id}'>";
        }else{
            $fhash = simple_hash(filepath_to_url($fp));
            echo "<div class=\"dl-btn\" ";
            echo "id=\"".$info['id']."\" fhash=\"".$fhash."\" ";
            echo "dlurl=\"".filepath_to_url($fp)."\" ";
            echo "parent='f_{$parent}'>";
        }
        // echo "<div class=\"sel\"><i class=\"material-icons\">check_box_outline_blank</i></div>";
        echo "<div class='td br' style=\"text-align:left; ";
        echo "padding-left:".($level*1.3 + 0.3)."em;";
        echo "\">";
        $f_cell = str_replace($slug, "<sub>{$slug}</sub>", $f);
        $f_cell = str_replace("_", "<em>_</em>", $f_cell);
        $f_cell = format_icon($slug, $fp).$f_cell;
        echo $is_dir ? $f_cell : ($level > 0 ? "↳ " : "").'<b>'.$f_cell.'</b>';
        echo "</div>";
        $img_size = $is_dir ? NULL : getimagesize($fp);
        if(@is_array($img_size)){
            echo "<div class='td fs'>";
            echo $img_size[0]."𝗑".$img_size[1];
            echo "</div>";
        }
        if (!$is_dir){
            echo "<div class='td fs'>";
            echo human_filesize(filesize($fp));
            echo "</div>";
        }
        echo "</div>";
        echo $is_dir ? "" : "</a>";
        if ($is_dir){
            fill_file_table($info, $fp, array_merge($parents, [$id]));
        }
    }
}

function format_icon($slug, $fp){
    if (is_dir($fp)){
        return "<i class=\"material-icons icon\">folder_open</i>";
    }
    if (is_image_file($fp)){
        $icon_path = get_thumbnail($fp, 24, 55);
        return "<img src=\"".filepath_to_url($icon_path)."\" alt=\"icon ".basename($fp)."\" class='icon' />";
    }
    $ext = strtolower(pathinfo($fp, PATHINFO_EXTENSION));
    $icon_path = join_paths($GLOBALS['SYSTEM_ROOT'], "files/site_images/icons/file_types", "{$ext}.svg");
    if (file_exists($icon_path)){
        return "<img src=\"".filepath_to_url($icon_path)."\" class='icon' />";
    }
    return "<img src=\"/files/site_images/icons/file.svg\" class='icon' />";
}


?>
