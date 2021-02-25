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
    <h1>Creators:</h1><!--
    --><ul><!--
    --><li><img class='me-med' src="/files/site_images/authors/Greg Zaal.jpg" /><!--
    --><p><b>Greg Zaal</b><!--
    --><br><!--
    --><a href="https://hdrihaven.com"><i class='material-icons'>link</i></a><!--
    --><a href="mailto:info@3dmodelhaven.com"><i class='material-icons'>mail_outline</i></a><!--
    --><br><!--
    --><em>Founder</em><!--
    --></p><!--
    --></li><!--
    --><li><img class='me-med' src="/files/site_images/authors/Cameron Casey.jpg" /><!--
    --><p><b>Cameron Casey</b><!--
    --><br><!--
    --><a href="https://www.artstation.com/cameroncasey"><i class='material-icons'>link</i></a><!--
    --><br><!--
    --><em>Founder</em><!--
    --></p><!--
    --></li><?php
    $conn = db_conn_read_only();
    $row = 0; // Default incase of SQL error
    $sql = "SELECT authors.*, (SELECT COUNT(*) FROM models WHERE author = authors.name) as num_models FROM authors ORDER BY num_models DESC, name ASC";
    $result = mysqli_query($conn, $sql);
    $array = array();
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($array, $row);
        }
    }

    foreach ($array as $a){
        $author_pic = join_paths($GLOBALS['SYSTEM_ROOT'], "/files/site_images/authors/".$a['name'].".jpg");
        if (file_exists($author_pic) && $a['num_models'] > 0){
            $author_pic = filepath_to_url(get_thumbnail($author_pic, 100, 85));
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
            echo $a['num_models'];
            echo $a['num_models'] != 1 ? " models" : " model";
            echo "</a>";
            echo "</p>";
            echo "</li>";
        }
    }
    ?>
    </ul>
    </div>

    <?php
    insert_commercial_sponsors($heading="Corporate Sponsors:", $reuse_conn=(isset($conn) ? $conn : NULL));
    ?>

    <h1>How you can help</h1>
    <h2>1. Support us on Patreon</h2>
    <p>
        If you have some disposable income and you want to help us publish more free 3D models, you can support us with a small monthly donation on <a href="https://polyhaven.com/support-us">Patreon</a> :)
    </p>
    <p>
        In return we can give you some small token of thanks, like access to a Google Drive folder that you can sync to your hard drive so you always have our latest assets at your fingertips, or immortalize your name in an empty portion of the diffuse map of a model of your choice.
    </p>
    <p>
        We use your donations directly to cover the site expenses and purchase new assets from artists, as verified by our <a href="/p/finance-reports.php">monthly finance reports</a>.
    </p>
    <h2>2. Donate your 3D models</h2>
    <p>
        Have a top-notch 3D model you'd like to share with the community? We'd love to publish it for you :)
    </p>
    <p>
        We have a strict standard of quality to maintain, and everything we publish has to be CC0, but if your asset is accepted the whole 3D community will benefit from it. <a href="/p/donate-model.php">Read more and submit your asset here.</a>
    </p>
    <h2>3. Spread the word</h2>
    <p>
        It's OK if you can't afford or don't want to donate to us :) You can still help us grow by making sure your friends and co-workers know about us, the more people we can help the better!
    </p>
    <p>
        It's not required at all, but if you use our assets in your work you can mention where you got them from and that'll help show more people that we exist.
    </p>

    <h1>Contact</h1>
    <p>Got a question? Please read the <a href="/p/faq.php">FAQ</a> first :)</p>
    <p>The easiest ways to get hold of us is through email: <?php insert_email() ?></p>

</div>

<?php
include ($_SERVER['DOCUMENT_ROOT'].'/php/html/footer.php');
include ($_SERVER['DOCUMENT_ROOT'].'/php/html/end_html.php');
?>
