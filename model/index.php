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

$canonical = "https://3dmodelhaven.com/tex/?t=".$slug;
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
echo "<a href='/models/?c=all'>";
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
    echo "<img src=\"/files/mod_images/renders/{$slug}.png\" />";
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

    if ($GLOBALS['WORKING_LOCALLY'] && is_in_the_past($info['date_published']) == False){
        echo "<p style='text-align:center;opacity:0.5;'>(working locally on a yet-to-be-published model)</p>";
    }
    echo "<div id='item-info'>";

    echo "<ul class='item-info-list'>";

    echo "<li>";
    echo "Author:<br/><b><a href=\"/models/?a=".$info['author']."\">".$info['author']."</a>";
    echo "</b></li>";

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

    $downloads_per_day = round($info['download_count']/((time() - strtotime($info['date_published']))/86400));
    echo "<li>";
    echo "Downloads:<br/><b>".$info['download_count']."</b> (".$downloads_per_day." per day)";
    echo "</li>";

    echo "<li>";
    echo "License:<br/><b><a href='/p/license.php'>CC0</a>";
    echo "</b></li>";

    echo "</ul>";

    echo "</div>";  // .item-info
    echo "</div>";  // #preview-info


    echo "<div class='download-buttons'>";
    echo "<h2>Downloads:</h2>";
    $downloads = [];
    $base_dir = join_paths($GLOBALS['SYSTEM_ROOT'], "files", "models", $slug);
    echo "<div class='fake-table'>";
    fill_file_table($info, $base_dir);
    echo "</div>";  // .fake-table
    echo "</div>";  // .download-buttons


    $similar = get_similar($slug, $conn);
    if ($similar){
        echo "<h2>";
        echo "Similar Models";
        echo "</h2>";
        echo "<div id='similar-models'>";
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

if (!$GLOBALS['WORKING_LOCALLY']){
    echo "<hr class='disqus' />";
    include_disqus('tex_'.$slug);
}

echo "</div>";  // #page-wrapper
echo "</div>";  // #item-page
?>


<?php
include ($_SERVER['DOCUMENT_ROOT'].'/php/html/footer.php');
include ($_SERVER['DOCUMENT_ROOT'].'/php/html/end_html.php');
?>
