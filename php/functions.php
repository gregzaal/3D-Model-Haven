<?php

include($_SERVER['DOCUMENT_ROOT'].'/php/secret_config.php');
include($_SERVER['DOCUMENT_ROOT'].'/php/utils.php');
include($_SERVER['DOCUMENT_ROOT'].'/php/database.php');

// ============================================================================
// HTML
// ============================================================================

function include_start_html($title, $slug="", $canonical="", $t1="") {
    ob_start();
    include $_SERVER['DOCUMENT_ROOT']."/php/html/start_html.php";
    $html = ob_get_contents();
    $textures_one = "";
    ob_end_clean();

    if ($title == "Render Gallery"){
        $html = str_replace('%GALLERYJS%', "<link rel=\"stylesheet\" href=\"/js/flexImages/jquery.flex-images.css\"><script src=\"/js/flexImages/jquery.flex-images.min.js\"></script>", $html);
    }else{
        $html = str_replace('%GALLERYJS%', "", $html);
    }

    if ($title != "3D Model Haven"){
        $title .= " | 3D Model Haven";
        $html = str_replace('%LANDINGJS%', "", $html);
    }else{
        $html = str_replace('%LANDINGJS%', "<script src='/js/landing-slider.js'></script>", $html);
        $textures_one = "<!-- START Textures.one integration -->
        <meta name=\"tex1:display-name\" content=\"3D Model Haven\" />
        <meta name=\"tex1:display-domain\" content=\"3dmodelhaven.com\" />
        <meta name=\"tex1:patreon\" content=\"3dmodelhaven\" />
        <meta name=\"tex1:twitter\" content=\"ModelHaven\" />
        <meta name=\"tex1:instagram\" content=\"\" />
        <meta name=\"tex1:logo\" content=\"https://3dmodelhaven.com/files/site_images/logo_line_tmp.png\" />
        <meta name=\"tex1:icon\" content=\"https://3dmodelhaven.com/favicon.png\" />
        <!-- END Textures.one integration -->";
    }
    $html = str_replace('%TITLE%', $title, $html);

    $html = str_replace('%METATITLE%', $title, $html);
    $html = str_replace('%DESCRIPTION%', '100% Free High Quality 3D Models for Everyone', $html);
    $keywords = 'Texture,PBR,free,cc0,creative commons';
    if ($t1 != ""){
        $keywords = $t1['tags'] . "," . $keywords;
    }
    $html = str_replace('%KEYWORDS%', $keywords, $html);

    $author = "Rob Tuytel, Greg Zaal";
    if ($t1 != ""){
        $author = $t1['author'];
    }
    $html = str_replace('%AUTHOR%', $author, $html);

    if ($canonical != ""){
        $html = str_replace('%URL%', $canonical, $html);
    }else{
        $html = str_replace('%URL%', "https://3dmodelhaven.com$_SERVER[REQUEST_URI]", $html);
    }

    if ($slug != ""){
        $preview_img = "https://3dmodelhaven.com/files/tex_images/spheres/{$slug}.jpg";
        $html = str_replace('%FEATURE%', $preview_img, $html);
        if ($t1 != ""){
            $textures_one = "<!-- START Textures.one integration -->\n";
            $textures_one .= "<meta name=\"tex1:name\" content=\"".$t1['name']."\" />\n";
            $textures_one .= "<meta name=\"tex1:tags\" content=\"".$t1['tags']."\" />\n";
            $textures_one .= "<meta name=\"tex1:preview-image\" content=\"$preview_img\" />\n";
            // $textures_one .= "<meta name=\"tex1:type\" content=\"\" />\n";
            $textures_one .= "<meta name=\"tex1:releasedate\" content=\"".$t1['date_published']."\" />\n";
            $textures_one .= "<!-- END Textures.one integration -->";
        }
    }else{
        $html = str_replace('%FEATURE%', "https://3dmodelhaven.com/feature.jpg", $html);
    }

    $html = str_replace('%TEXTURESONE%', $textures_one, $html);

    echo $html;
}

function include_disqus($id) {
    ob_start();
    include $_SERVER['DOCUMENT_ROOT']."/php/html/disqus.php";
    $html = ob_get_contents();
    ob_end_clean();

    $id = str_replace("'", "\'", $id);
    echo str_replace('%ID%', $id, $html);
}

function insert_email($text="##email##"){
    echo '<script type="text/javascript">';
    echo 'var s3 = "3dmodelhaven.com";';
    echo 'var s1 = "info";';
    echo 'var s2 = "@";';
    echo 'var s4 = s1 + s2 + s3;';
    echo 'document.write("<a href=" + "mail" + "to:" + s1 + s2 + s3 + " target=\"_blank\">';
    if ($text == "##email##"){
        echo '" + s4 + "';
    }else{
        echo $text;
    }
    echo '</a>");';
    echo '</script>';
}


// ============================================================================
// Model Grid
// ============================================================================

function make_category_list($sort, $reuse_conn=NULL, $current="all"){
    if (is_null($reuse_conn)){
        $conn = db_conn_read_only();
    }else{
        $conn = $reuse_conn;
    }
    echo "<div class='category-list-wrapper'>";
    echo "<ul id='category-list'>";
    $categories = get_all_categories($conn);
    array_unshift($categories, "all");
    foreach ($categories as $c){
        if ($c){  // Ignore uncategorized
            $num_in_cat = num_items("all", $c, $conn);
            echo "<a href='/models/?c=".$c."&amp;o={$sort}'>";
            echo "<li title=\"".nice_name($c)."\"";
            if ($current != "all" && $c == $current){
                echo " class='current-cat'";
            }
            echo ">";
            echo "<i class=\"material-icons\">keyboard_arrow_right</i>";
            echo nice_name($c, "category");
            echo "<div class='num-in-cat'>".$num_in_cat."</div>";
            echo "</li>";
            echo "</a>";

            if ($c != 'all' && $c == $current){
                $tags_in_cat = get_all_tags($c, $conn);
                $last_tag = end($tags_in_cat);
                foreach ($tags_in_cat as $t){
                    echo "<a href='/models/?c=".$c."&amp;s={$t}"."&amp;o={$sort}'>";
                    echo "<li class='tag";
                    if ($t == $last_tag){
                        echo " last-tag";
                    }
                    echo "'>";
                    echo "<i class=\"material-icons\">keyboard_arrow_right</i>";
                    echo nice_name($t);
                    echo "</li>";
                    echo "</a>";
                }
            }
        }
    }
    echo "</ul>";
    echo "</div>";
}

function make_grid_item($i, $category="all"){
    $html = "";

    $slug = $i['slug'];
    $html .= "<a href=\"/tex/?";
    if ($category != "all"){
        $html .= "c=".$category."&amp;";
    }
    $html .= "t=".$slug;
    $html .= "\">";
    $html .= "<div class='grid-item'>";

    $html .= "<div class='thumbnail-wrapper'>";
    $img_url = "/files/tex_images/thumbnails/{$slug}.jpg";
    $local_image_path = join_paths($GLOBALS['SYSTEM_ROOT'], $img_url);
    if ($GLOBALS['WORKING_LOCALLY'] and !file_exists($local_image_path)){
        $img_url = "http://3dmodelhaven.com".$img_url;
    }
    $html .= "<img ";
    $html .= "class='thumbnail' ";
    $html .= "src=\"{$img_url}\" ";
    $html .= "alt=\"3D Model: {$i['name']}\" ";
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

    $html .= "<p class='age'>".time_ago($i['date_published'])."</p>";

    $html .= "</div>";  // description

    $html .= "</div>";  // description-wrapper

    $html .= "</div>";  // grid-item
    $html .= "</a>";

    return $html;
}

function make_item_grid($sort="popular", $search="all", $category="all", $author="all", $conn=NULL, $limit=0){
    $items = get_from_db($sort, $search, $category, $author, $conn, $limit);
    $html = "";
    if (!$items) {
        $html .= "<p>Sorry! There are no models";
        if ($search != 'all'){
            $html .= " that match the search \"".htmlspecialchars($search)."\"";
        }
        if ($category != 'all'){
            $html .= " in the category \"".nice_name($category, "category")."\"";
        }
        if ($author != 'all'){
            $html .= " by ".$author;
        }
        $html .= " :(</p>";
    }else{
        if ($search != "all"){
            $html .= "<h2 style='padding: 0; margin: 0'>";
            $html .= sizeof($items);
            $html .= " results";
            $html .= "</h2>";
        }
        foreach ($items as $i){
            $html .= make_grid_item($i, $category);
        }
    }
    return $html;
}


// ============================================================================
// Patreon
// ============================================================================

function pledge_rank($pledge_amount){
    $pledge_rank = 1;
    if ($pledge_amount >= 2000) {
        $pledge_rank = 5;
    }else if ($pledge_amount >= 1000){
        $pledge_rank = 4;
    }else if ($pledge_amount >= 500){
        $pledge_rank = 3;
    }else if ($pledge_amount >= 300){
        $pledge_rank = 2;
    }
    return $pledge_rank;
}

function get_name_changes($reuse_conn=NULL){
    if (is_null($reuse_conn)){
        $conn = db_conn_read_only();
    }else{
        $conn = $reuse_conn;
    }

    $sql = "SELECT * FROM patron_name_mod";
    $result = mysqli_query($conn, $sql);
    $array = array();
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $array[$row['id']] = $row;
        }
    }
    if (is_null($reuse_conn)){
        $conn->close();
    }

    $name_replacements = [];
    $add_names = [];
    $remove_names = [];
    foreach ($array as $i){
        $n_from = $i['n_from'];
        $n_to = $i['n_to'];
        if ($n_to and $n_from){
            $name_replacements[$n_from] = $n_to;
        }else if($n_to and !$n_from){
            $add_names[$n_to] = $i['rank'];
        }else{
            array_push($remove_names, $n_from);
        }
    }

    return [$name_replacements, $add_names, $remove_names];
}

function get_patreon(){
    $patreoncache = $_SERVER['DOCUMENT_ROOT'].'/php/patreon_data/_latest.json';

    // Some users request name change
    $conn = db_conn_read_only();
    list($name_replacements, $add_names, $remove_names) = get_name_changes($conn);

    // Get dummy data if working locally
    if ($GLOBALS['WORKING_LOCALLY']){
        $example_names = [
            "Joni Mercado",
            "S J Bennett",
            "Adam Nordgren",
            "RENDER WORX",
            "Pierre Beranger",
            "Pablo Lopez Soriano",
            "Frank Busch",
            "Sterling Roth",
            "Jonathan Sargent",
            "hector gil",
            "Philip bazel",
            "Llynara",
            "BlenderBrit",
            "william norberg",
            "Michael Szalapski",
        ];
        $patron_list = [];
        for ($i=0; $i<100; $i++){
            $pledge_rank_weights = [1,1,1,1, 2,2,2,2,2,2,2,2,2,2,2,2, 3,3,3, 4,4, 5];
            $pledge_rank = $pledge_rank_weights[array_rand($pledge_rank_weights)];
            $patron_full_name = $example_names[array_rand($example_names)];
            if (array_key_exists($patron_full_name, $name_replacements)){
                $patron_full_name = $name_replacements[$patron_full_name];
            }

            if (!in_array($patron_full_name, $remove_names)){
                array_push($patron_list, [$patron_full_name, $pledge_rank]);
            }
        }
        foreach (array_keys($add_names) as $p){
            array_splice($patron_list, rand(0, sizeof($patron_list)-1), 0, [[$p, $add_names[$p]]]);
        }


        $goals = [
            [
            "amount_cents" => 150000,
            "completed_percentage" => 83,
            "description" => "<strong>Test Goal Title<br><br></strong>Test goal description :).</em>"
            ],
        ];

        $goals = array_sort($goals, "amount_cents", SORT_ASC);

        $data = [$patron_list, 1247, $goals];

        // Write to cache
        file_put_contents($patreoncache, json_encode($data, JSON_PRETTY_PRINT));

        return $data;
    }

    // Cache to avoid overusing Patreon API
    $cachetime = 120;  // How many minutes before the cache is invalid
    $cachetime *= 60;  // convert to seconds
    if (file_exists($patreoncache)) {
        if (time() - $cachetime < filemtime($patreoncache)){
            // echo "<!-- Patreon cache ".date('H:i', filemtime($patreoncache))." -->\n";
            $str = file_get_contents($patreoncache);
            return json_decode($str, true);
        }else{
            // Keep old cache file for statistical purposes
            rename($patreoncache, $_SERVER['DOCUMENT_ROOT'].'/php/patreon_data/'.time().'.json');
        }
    }

    $patreon_tokens = [];
    $patreon_tokens_path = $_SERVER['DOCUMENT_ROOT'].'/php/patreon_tokens.json';
    if (file_exists($patreon_tokens_path)){
        $str = file_get_contents($patreon_tokens_path);
        $patreon_tokens = json_decode($str, true);
    }
    $access_token = $patreon_tokens["access_token"];
    $refresh_token = $patreon_tokens["refresh_token"];
    $api_client = new Patreon\API($access_token);
    // Get your campaign data
    $campaign_response = $api_client->fetch_campaign();

    // If the token doesn't work, get a newer one
    if ($campaign_response['errors']) {
        echo "Got an error\n";
        echo "Refreshing tokens\n";
        // Make an OAuth client
        $client_id = $GLOBALS['CLIENT_ID'];
        $client_secret = $GLOBALS['CLIENT_SECRET'];
        $oauth_client = new Patreon\OAuth($client_id, $client_secret);
        // Get a fresher access token
        $tokens = $oauth_client->refresh_token($refresh_token, null);
        debug_email("Patreon Tokens", json_encode($tokens, JSON_PRETTY_PRINT));
        if ($tokens['access_token']) {
            $access_token = $tokens['access_token'];
            $fp = fopen($patreon_tokens_path, 'w');
            fwrite($fp, json_encode($tokens));
            fclose($fp);
            echo "Got a new access_token!";
        } else {
            echo "Can't fetch new tokens. Please debug, or write in to Patreon support.\n";
            print_r($tokens);
        }
        $api_client = new Patreon\API($access_token);
        $campaign_response = $api_client->fetch_campaign();
    }

    // get page after page of pledge data
    $campaign_id = $campaign_response['data'][0]['id'];
    $cursor = null;
    $patron_list = [];
    $total_earnings_c = 0;
    while (true) {
        $pledges_response = $api_client->fetch_page_of_pledges($campaign_id, 25, $cursor);
        // get all the users in an easy-to-lookup way
        $user_data = [];
        foreach ($pledges_response['included'] as $included_data) {
            if ($included_data['type'] == 'user') {
                $user_data[$included_data['id']] = $included_data;
            }
        }
        // loop over the pledges to get e.g. their amount and user name
        foreach ($pledges_response['data'] as $pledge_data) {
            $declined = $pledge_data['attributes']['declined_since'];
            if (!$declined){
                $pledge_amount = $pledge_data['attributes']['amount_cents'];
                $total_earnings_c += $pledge_amount;
                $pledge_rank = pledge_rank($pledge_amount);

                $patron_id = $pledge_data['relationships']['patron']['data']['id'];
                $patron_full_name = $user_data[$patron_id]['attributes']['full_name'];

                if (array_key_exists($patron_full_name, $name_replacements)){
                    $patron_full_name = $name_replacements[$patron_full_name];
                }

                if (!in_array($patron_full_name, $remove_names)){
                    array_push($patron_list, [$patron_full_name, $pledge_rank]);
                }
            }
        }
        // get the link to the next page of pledges
        $next_link = $pledges_response['links']['next'];
        if (!$next_link) {
            // if there's no next page, we're done!
            break;
        }
        // otherwise, parse out the cursor param
        $next_query_params = explode("?", $next_link)[1];
        parse_str($next_query_params, $parsed_next_query_params);
        $cursor = $parsed_next_query_params['page']['cursor'];
    }
    foreach (array_keys($add_names) as $p){
        array_splice($patron_list, rand(0, sizeof($patron_list)-1), 0, [[$p, $add_names[$p]]]);
    }

    $tmp = $campaign_response['included'];
    $goals = [];
    foreach ($tmp as $x){
        if ($x['type'] == 'goal'){
            array_push($goals, $x['attributes']);
        }
    }

    $goals = array_sort($goals, "amount_cents", SORT_ASC);

    $data = [$patron_list, $total_earnings_c/100, $goals];

    // Write to cache
    file_put_contents($patreoncache, json_encode($data, JSON_PRETTY_PRINT));
    return $data;
}

function goal_title($g){
    $d = $g['description'];
    $bits = explode("</strong>", $d);
    $t = $bits[0];
    $t = str_replace("<strong>", "", $t);
    $t = str_replace("<br>", "", $t);
    return $t;
}


?>
