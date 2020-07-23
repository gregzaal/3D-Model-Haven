<?php
include ($_SERVER['DOCUMENT_ROOT'].'/php/functions.php');
include_start_html("About / Contact");
include ($_SERVER['DOCUMENT_ROOT'].'/php/html/header.php');
?>

<div id="page-wrapper">
    <div class='me-wrapper'>
        <img style="max-width:256px;float:left;margin-right:2em" src="/core/img/Model Haven Logo.svg">
    </div>
    <h1>Hi there!</h1>
    <p>
        3D Model Haven is where you can find high quality 3D models for free, no catch.
    </p>
    <p>
        All 3D models here are <a href="/p/license.php">CC0</a> (public domain). No paywalls, accounts or email spam. Just download what you want, and use it however.
    </p>
    <p>
        3D Model Haven is officially linked with <a href="https://hdrihaven.com">HDRI Haven</a> and <a href="https://texturehaven.com">Texture Haven</a>.
    </p>

    <div style="clear: both"></div>

    <div class="author-list">
    <h1>Creators: </h1>
    <ul>

    <li><img class='me-med' src="/files/site_images/authors/Greg Zaal.jpg" />
    <p><b>Greg Zaal</b>
    <br>
    <a href="https://hdrihaven.com"><i class='material-icons'>link</i></a>
    <a href="mailto:info@3dmodelhaven.com"><i class='material-icons'>mail_outline</i></a>
    <br>
    <em>Founder</em>
    </p>
    </li>

    <li><img class='me-med' src="/files/site_images/authors/Cameron Casey.jpg" />
    <p><b>Cameron Casey</b>
    <br>
    <a href="https://www.artstation.com/cameroncasey"><i class='material-icons'>link</i></a>
    <br>
    <em>Founder</em>
    </p>
    </li>

    <?php
    $conn = db_conn_read_only();
    $row = 0; // Default incase of SQL error
    $sql = "SELECT * FROM authors ORDER BY `id`";
    $result = mysqli_query($conn, $sql);
    $array = array();
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($array, $row);
        }
    }

    $items = get_from_db("popular", "all", "all", "all", $conn);

    foreach ($array as $a){
        $author_pic = join_paths($GLOBALS['SYSTEM_ROOT'], "/files/site_images/authors/".$a['name'].".jpg");
        $n_items = 0;
        foreach ($items as $i){
            if ($i['author'] == $a['name']){
                $n_items++;
            }
        }
        if (file_exists($author_pic) && $n_items > 0){
            $author_pic = filepath_to_url(get_thumbnail($author_pic, 150, 85));
            echo "<li>";
            echo "<img class='me-med' src=\"".$author_pic."\" />";
            echo "<p>";
            echo "<b>".$a['name']."</b>";
            echo "<br>";
            if ($a['link']){
                echo "<a href=\"".$a['link']."\">";
                echo "<i class='material-icons'>link</i>";
                echo "</a>";
            }
            if ($a['email']){
                echo "<a href=\"mailto:".$a['email']."\">";
                echo "<i class='material-icons'>mail_outline</i>";
                echo "</a>";
            }
            if ($a['donate']){
                if (starts_with($a['donate'], "paypal:")){
                    $a['donate'] = str_replace("paypal:", "", $a['donate']);
                    $a['donate'] = paypal_email_to_link($a['donate'], "3D Model Haven");
                }
                echo "<a href=\"".$a['donate']."\">";
                echo "<i class='material-icons'>favorite_border</i>";
                echo "</a>";
            }
            echo "<br>";
            echo "<a href=\"/models/?a=".$a['name']."\">";
            echo $n_items;
            echo $n_items != 1 ? " models" : " model";
            echo "</a>";
            echo "</p>";
            echo "</li>";
        }
    }
    ?>
    </ul>
    </div>

    <h1>Get Involved</h1>
    <p>
        Since all of the income for this site comes from the community, it's only fair that the community gets to decide what happens with it.
    </p>
    <p>
        All Patrons have access to a private Trello board where they can add ideas and vote on new types of models, and generally decide where the money goes.
    </p>
    <p>
        If you want to get involved and help keep this site alive at the same time, consider supporting <a href="https://www.patreon.com/3dmodelhaven/overview">3D Model Haven on Patreon</a>.
    </p>

    <h1>Contact</h1>
    <p>Got a question? Please read the <a href="/p/faq.php">FAQ</a> first :)</p>
    <p>The easiest ways to get hold of me is through email: <?php insert_email() ?></p>

</div>

<?php
include ($_SERVER['DOCUMENT_ROOT'].'/php/html/footer.php');
include ($_SERVER['DOCUMENT_ROOT'].'/php/html/end_html.php');
?>
