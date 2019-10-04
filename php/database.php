<?php

include($_SERVER['DOCUMENT_ROOT'].'/php/secret_config.php');
include($_SERVER['DOCUMENT_ROOT'].'/php/utils.php');


function db_conn_read_only(){
    $servername = $GLOBALS['DB_SERV'];
    $dbname = $GLOBALS['DB_NAME'];
    $username = $GLOBALS['DB_USER_R'];
    $password = $GLOBALS['DB_PASS_R'];
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}


function db_conn_read_write(){
    $servername = $GLOBALS['DB_SERV'];
    $dbname = $GLOBALS['DB_NAME'];
    $username = $GLOBALS['DB_USER'];
    $password = $GLOBALS['DB_PASS'];
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}


function num_items($search="all", $category="all", $reuse_conn=NULL){
    $size = 0;

    // Create connection
    if (is_null($reuse_conn)){
        $conn = db_conn_read_only();
    }else{
        $conn = $reuse_conn;
    }
    $search_text = make_search_SQL(mysqli_real_escape_string($conn, $search), $category, "all");

    $sql = "SELECT name FROM models ".$search_text;
    $rows = mysqli_query($conn, $sql)->num_rows;

    if (is_null($reuse_conn)){
        $conn->close();
    }

    return $rows;
}


function make_sort_SQL($sort) {
    // Return the ORDER BY part of an SQL query based on the sort method
    $sql = "ORDER BY id DESC";
    switch ($sort) {
        case "date_published":
            $sql = "ORDER BY date_published DESC, download_count DESC, slug ASC";
            break;
        case "popular":
            $sql = "ORDER BY download_count/POWER(ABS(DATEDIFF(date_published, NOW()))+1, 1.2) DESC, download_count DESC, slug ASC";
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


function get_from_db($sort="popular", $search="all", $category="all", $author="all", $reuse_conn=NULL, $limit=0){
    $sort_text = make_sort_SQL($sort);

    // Create connection
    if (is_null($reuse_conn)){
        $conn = db_conn_read_only();
    }else{
        $conn = $reuse_conn;
    }
    $search_text = make_search_SQL(mysqli_real_escape_string($conn, $search), $category, $author);

    $sql = "SELECT * FROM models ".$search_text." ".$sort_text;
    if ($limit > 0){
        $sql .= " LIMIT ".$limit;
    }
    $result = mysqli_query($conn, $sql);

    $array = array();
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $array[$row['name']] = $row;
        }
    }
    if (is_null($reuse_conn)){
        $conn->close();
    }

    return $array;
}


function get_item_from_db($item, $reuse_conn=NULL){
    if (is_null($reuse_conn)){
        $conn = db_conn_read_only();
    }else{
        $conn = $reuse_conn;
    }
    $row = 0; // Default incase of SQL error
    $sql = "SELECT * FROM models WHERE slug='".$item."'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    }

    if (is_null($reuse_conn)){
        $conn->close();
    }

    return $row;
}


function get_all_cats_or_tags($mode, $cat="all", $conn=NULL){
    $db = get_from_db("popular", "all", $cat, "all", $conn, 0);
    $all_flags = [];
    foreach ($db as $item){
        $flags = explode(";",  str_replace(',', ';', $item[$mode]));
        foreach ($flags as $t){
            $t = strtolower($t);
            if (!in_array($t, $all_flags)){
                array_push($all_flags, $t);
            }
        }
    }
    sort($all_flags);
    return $all_flags;
}


function get_all_categories($conn=NULL){
    // Convenience function
    return get_all_cats_or_tags("categories", "all", $conn);
}


function get_all_tags($cat="all", $conn=NULL){
    // Convenience function
    return get_all_cats_or_tags("tags", $cat, $conn);
}


function track_search($search_term, $category="", $reuse_conn=NULL){
    if ($search_term != "all"){
        if (is_null($reuse_conn)){
            $conn = db_conn_read_write();
        }else{
            $conn = $reuse_conn;
        }
        $search_term = mysqli_real_escape_string($conn, $search_term);
        $category = mysqli_real_escape_string($conn, $category);

        $sql = "INSERT INTO searches (`category`, `search_term`) ";
        $sql .= "VALUES (\"".$category."\", \"".$search_term."\")";
        $result = mysqli_query($conn, $sql);

        if (is_null($reuse_conn)){
            $conn->close();
        }
    }
}


function get_similar($slug, $reuse_conn=NULL){

    if (is_null($reuse_conn)){
        $conn = db_conn_read_only();
    }else{
        $conn = $reuse_conn;
    }
    $items = get_from_db("popular", "all", "all", "all", $conn);
    if (is_null($reuse_conn)){
        $conn->close();
    }

    $this_item = array();
    foreach ($items as $row){
        if ($row['slug'] == $slug){
            $this_item = $row;
            break;
        }
    }
    if (!$this_item){
        // Unpublished items will not be in 'get_from_db', so just don't show their similar items
        return NULL;
    }
    $similarities = array();
    foreach ($items as $row){
        $row_slug = $row['slug'];
        if ($row_slug != $slug){
            $cats = explode(";", $row['categories']);
            foreach ($cats as $cat){
                if (strpos((';'.$this_item['categories'].';'), (';'.$cat.';')) !== FALSE){
                    if (array_key_exists($row_slug, $similarities)){
                        $similarities[$row_slug] = $similarities[$row_slug] + 1;
                    }else{
                        $similarities[$row_slug] = 1;
                    }
                }
            }
            $tags = explode(";", $row['tags']);
            foreach ($tags as $tag){
                if (strpos((';'.$this_item['tags'].';'), (';'.$tag.';')) !== FALSE){
                    if (array_key_exists($row_slug, $similarities)){
                        $similarities[$row_slug] = $similarities[$row_slug] + 1;
                    }else{
                        $similarities[$row_slug] = 1;
                    }
                }
            }
        }
    }
    arsort($similarities);
    $similar_slugs = array_slice(array_keys($similarities), 0, 6);  // only the first 6 keys

    $similar = array();
    foreach ($similar_slugs as $s){
        foreach ($items as $i){
            if ($i['slug'] == $s){
                array_push($similar, $i);
            }
        }
    }

    return $similar;
}


function most_popular_in_each_category($reuse_conn=NULL){
    // Return array with single most popular item for each category (keys)

    if (is_null($reuse_conn)){
        $conn = db_conn_read_only();
    }else{
        $conn = $reuse_conn;
    }

    $a = [];
    $items = get_from_db("popular", "all", "all", "all", $conn);
    foreach (get_all_categories($conn) as $c){
        $found = false;
        foreach ($items as $h){
            $category_arr = explode(';', $h['categories']);
            if (in_array($c, $category_arr) or $c == "all"){
                $last_of_cat = $h;  // In case no unused match is found
                if (!in_array($h['slug'], array_values($a))){
                    $a[$c] = $h['slug'];
                    $found = true;
                    break;
                }
            }
        }
        if (!$found){
            $a[$c] = $last_of_cat['slug'];
        }
    }

    if (is_null($reuse_conn)){
        $conn->close();
    }

    return $a;
}


function get_gallery_renders($reuse_conn=NULL){
    if (is_null($reuse_conn)){
        $conn = db_conn_read_only();
    }else{
        $conn = $reuse_conn;
    }
    $row = 0; // Default incase of SQL error
    $sql = "SELECT * FROM gallery WHERE favourite=1 OR TIMESTAMPDIFF(DAY, date_added, now()) < 21 ORDER BY POWER(clicks+10*click_weight, 0.7)/POWER(ABS(DATEDIFF(date_added, NOW()))+1, 1.1) DESC, clicks DESC, date_added DESC";
    $result = mysqli_query($conn, $sql);

    $array = array();
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($array, $row);
        }
    }
    if (is_null($reuse_conn)){
        $conn->close();
    }

    return $array;
}


?>
