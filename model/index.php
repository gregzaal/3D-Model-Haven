<?php
include ($_SERVER['DOCUMENT_ROOT'].'/php/functions.php');

// Parameters
// Defaults:
$slug = "none";
$category = "all";

// Get params (if they were passed)
if (isset($_GET["m"]) && trim($_GET["m"])){
    $slug = $_GET["m"];
}
if (isset($_GET["c"]) && trim($_GET["c"])){
    $category = $_GET["c"];
}

// Redirect if parameters not received
if (empty($_GET["m"])){
    header("Location: /models/");
}

$slug = htmlspecialchars($slug);
$category = htmlspecialchars($category);

$conn = db_conn_read_only();
$info = get_item_from_db($slug, $conn);

// Redirect to search if the model is not in the DB.
if (sizeof($info) <= 1){
    header("Location: /models/?s=".$slug);
}

$canonical = "https://3dmodelhaven.com/model/?m=".$slug;
$t1 = [];
$t1 ['name'] = $info['name'];
$t1 ['date_published'] = $info['date_published'];
$t1 ['author'] = $info['author'];
$category_arr = explode(';', $info['categories']);
$tag_arr = explode(';', $info['tags']);
$tags = array_merge($category_arr, $tag_arr);
$t1 ['tags'] = implode(',', array_merge($category_arr, $tag_arr));
include_start_html($info['name'], $slug, $canonical, $t1);
include ($_SERVER['DOCUMENT_ROOT'].'/php/html/header.php');


echo "<div id='item-page'>";
echo "<div id='page-wrapper'>";

echo "<h1>";
echo "<a href='/models'>";
echo "3D Models";
echo "</a>";
echo " >";
if ($category != "all"){
    echo " ";
    echo "<a href='/models/?c={$category}'>";
    echo nice_name($category, 'category');
    echo "</a>";
    echo " >";
}
echo "<br><b>{$info['name']}</b></h1>";

$is_published = is_in_the_past($info['date_published']) || $GLOBALS['WORKING_LOCALLY'];
if ($is_published){
    echo "<div id='preview-info'>";

    echo "<div id='item-preview'>";
    $img = filepath_to_url(get_slug_thumbnail($slug, 640, 90));
    echo "<img src=\"{$img}\" />";
    echo "<div id='map-preview-img' class='hide'/>";
    echo "<div id='map-preview-zoom-btns' class='hide-mobile'>";
    echo "<div id='map-preview-resolution'>";
    echo "<span id='map-preview-resolution-select' class='button'>640p</span>";
    echo "<ul id='map-preview-resolution-list' class='hidden'>";
    echo "<li>1k</li>";
    echo "<li>2k</li>";
    echo "</ul>";  // #map-preview-resolution-list
    echo "</div>";  // #map-preview-resolution
    echo "<span class='map-preview-zoom' id='map-preview-zoom-out'>-</span>";
    echo "<span class='map-preview-zoom' id='map-preview-zoom-in'>+</span>";
    echo "</div>";  // #zoom-btns
    echo "</div>";  // #map-preview-img
    echo "</div>";  // #item-preview
    echo "<div id='item-info'>";

    echo "<ul class='item-info-list'>";

    echo "<li>";
    echo "Author:<br/><b><a href=\"/models/?a=".$info['author']."\">";
    $author_pic = join_paths($GLOBALS['SYSTEM_ROOT'], "/files/site_images/authors/".$info['author'].".jpg");
    if (file_exists($author_pic)){
        $author_pic = filepath_to_url(get_thumbnail($author_pic, 50, 85));
        echo "<img class='me-sml' src=\"".$author_pic."\" />";
    }
    echo $info['author'];
    echo "</a>";
    echo "</b>";
    $author_info = get_author_info($info['author'], $conn);
    if($author_info){
        echo "<span>";
        if ($author_info['link']){
            echo "<a href=\"".$author_info['link']."\">";
            echo "<i class='material-icons'>link</i>";
            echo "</a>";
        }
        if ($author_info['email']){
            echo "<a href=\"mailto:".$author_info['email']."\">";
            echo "<i class='material-icons'>mail_outline</i>";
            echo "</a>";
        }
        if ($author_info['donate']){
            if (starts_with($author_info['donate'], "paypal:")){
                $author_info['donate'] = str_replace("paypal:", "", $author_info['donate']);
                $author_info['donate'] = "https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=".$author_info['donate']."&item_name=3D Model Haven: ".$info['name'];
            }
            echo "<a href=\"".$author_info['donate']."\">";
            echo "<i class='material-icons'>favorite_border</i>";
            echo "</a>";
        }
        echo "</span>";
    }
    echo "</li>";

    echo "<li>";
    $category_str = "";
    $category_arr = explode(';', $info['categories']);
    sort($category_arr);
    foreach ($category_arr as $category) {
        $category_str .= '<a href="/models/?c='.$category.'">'.$category.'</a>, ';
    }
    $category_str = substr($category_str, 0, -2);  // Remove ", " at end
    echo "Categories:<br/><b>{$category_str}";
    echo "</b></li>";

    echo "<li>";
    $tag_str = "";
    $tag_arr = explode(';', $info['tags']);
    sort($tag_arr);
    foreach ($tag_arr as $tag) {
        $tag_str .= '<a href="/models/?s='.$tag.'">'.$tag.'</a>, ';
    }
    $tag_str = substr($tag_str, 0, -2);  // Remove ", " at end
    echo "Tags:<br/><b>{$tag_str}";
    echo "</b></li>";

    echo "<li>";
    echo "Published:<br/><b>".date("d F Y", strtotime($info['date_published']))."</b><br/>(".time_ago($info['date_published']).")";
    echo "</li>";

    $download_count = get_download_count($info['id'], $conn);
    $downloads_per_day = round($download_count/((time() - strtotime($info['date_published']))/86400));
    echo "<li>";
    echo "Downloads:<br/><b>{$download_count}</b> ({$downloads_per_day} per day)";
    echo "</li>";

    echo "<li>";
    echo "License:<br/><b><a href='/p/license.php'>CC0</a>";
    echo "</b></li>";

    echo "</ul>";

    echo "<div class='center'>";
    echo "<p class='small'>This model is sponsored by:</p>";
    echo "<ul id='sponsor-list'>";
    $sponsors = get_sponsors($slug, $conn);
    if ($sponsors){
        foreach ($sponsors as $s){
            echo "<li>";
            if ($s['url'] != "none" && $s['url'] != ""){
                echo "<a href=\"{$s['url']}\">";
            }
            echo $s['sponsor'];
            if ($s['url'] != "none" && $s['url'] != ""){
                echo "</a>";
            }
            echo "</li>";
        }
    }else{
        echo "<li style='font-weight:300'>";
        echo "No one yet :(";
        echo "</li>";
    }
    echo "</ul>";
    echo "<p class='small'><a href='https://www.patreon.com/3dmodelhaven/overview'>Support 3D Model Haven</a><br>to add your name here.</p>";
    echo "</div>";

    if ($info['tmwarn']){
        echo "<div class='warning-block'><p>";
        echo "<b>Warning:</b><br>";
        echo "This model may contain trademarks or may be based off an existing copyrighted design.<br>While the files below are CC0, use of this asset is at your own risk. ";
        insert_email("Contact us");
        echo " for more info.";
        echo "</p></div>";
    }

    echo "</div>";  // .item-info
    echo "</div>";  // #preview-info


    if ($GLOBALS['WORKING_LOCALLY'] && is_in_the_past($info['date_published']) == False){
        echo "<p style='text-align:center;opacity:0.5;'>(working locally on a yet-to-be-published model)</p>";
    }


    echo "<div class='download-buttons'>";
    $sub_dir = join_paths("files", "models", $slug);
    $base_dir = join_paths($GLOBALS['SYSTEM_ROOT'], $sub_dir);
    if (file_exists($base_dir)){
        $downloads = [];
        echo "<div class='main-download-buttons'>";
        echo "<div class='dl-icon'></div>";
        foreach (listdir($base_dir) as $f){
            if (basename(pathinfo($f, PATHINFO_FILENAME)) == $slug){
                $fp = join_paths($base_dir, $f);
                $ext = pathinfo($fp, PATHINFO_EXTENSION);
                $fhash = simple_hash(filepath_to_url($fp));
                $fsize = filesize($fp);
                $files_list_fp = $fp.".files";
                $btn_html = "<div class=\"dl-btn";
                $files = [];
                if (file_exists($files_list_fp)){
                    $files_json = json_decode(file_get_contents($files_list_fp));
                    array_push($files, [join_paths($sub_dir, $f), $f]);
                    foreach ($files_json as $af){
                        $real_fp = join_paths($base_dir, $af);
                        if (file_exists($real_fp)){
                            array_push($files, [join_paths($sub_dir, $af), $af]);
                            $fsize += filesize($real_fp);
                        }
                    }
                    $fhash = simple_hash(filepath_to_url($files_list_fp));
                }
                $do_zip = sizeof($files) > 1;
                if ($do_zip){
                    $btn_html .= " zip-dl";
                }
                $btn_html .= "\" id=\"".$info['id']."\" fhash=\"".$fhash."\">";
                $btn_html .= format_icon($slug, $fp);
                $btn_html .= "<p>".$ext."<br><sub>".human_filesize($fsize)."</sub></p>";
                if ($do_zip){
                    $files = json_encode($files);
                    $btn_html .= "<div class='zip-dl-files hidden' name='{$f}'>{$files}</div>";
                    $btn_html .= "<div class='zip-loading hidden'><div class='loading-animation'><div></div><div></div><div></div><div></div></div></div>";
                }
                $always_includes_textures = ['gltf'];
                if ($do_zip || in_array(strtolower($ext), $always_includes_textures)){
                    $btn_html .= "<div class='tooltip'>Textures included</div>";
                }else{
                    $btn_html .= "<div class='tooltip red-text'>No textures included, download them below.</div>";
                }
                $btn_html .= "</div>";  // .dl-btn
                if (!$do_zip){
                    $dl_url = filepath_to_url($fp);
                    $btn_html = "<a href=\"{$dl_url}\" download=\"{$f}\" target='_blank'>{$btn_html}</a>";
                }
                echo $btn_html;
            }
        }
        echo "<br><div id='sw-tab-warning' class='hidden'><p><i class='material-icons'>error_outline</i> Keep this tab open until your download has finished.<br>Closing this tab may cause the download to fail.</p></div>";
        echo "</div>";  // .main-download-buttons
        echo "<p class='center'>Additional files:</p>";
        echo "<div class='fake-table'>";
        fill_file_table($info, $base_dir);
        echo "</div>";  // .fake-table
    }else{
        echo "<p class='red-text center'><b>";
        echo "Can't find any files! Please ";
        insert_email("let us know");
        echo " that you're seeing this error.</b></p>";
    }
    echo "</div>";  // .download-buttons


    $similar = get_similar($slug, $conn);
    if ($similar){
        echo "<h2>";
        echo "Similar Models";
        echo "</h2>";
        echo "<div id='similar-items'>";
        echo "<div id='tex-grid'>";
        foreach ($similar as $s){
            echo make_grid_item($s);
        }
        echo "</div>";
        echo "</div>";
    }

}else{
    echo "<h1 class='coming-soon'>Coming soon :)</h1>";
}

/*
TODO:
    User renders
*/

// if (!$GLOBALS['WORKING_LOCALLY']){
//     echo "<hr class='disqus' />";
//     include_disqus('tex_'.$slug);
// }

echo "</div>";  // #page-wrapper
echo "</div>";  // #item-page
?>


<?php
include ($_SERVER['DOCUMENT_ROOT'].'/php/html/footer.php');
include ($_SERVER['DOCUMENT_ROOT'].'/php/html/end_html.php');
?>
