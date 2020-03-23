<?php
include ($_SERVER['DOCUMENT_ROOT'].'/php/functions.php');
include_start_html("About / Contact");
include ($_SERVER['DOCUMENT_ROOT'].'/php/html/header.php');
?>

<div id="page-wrapper">
    <div class='me-wrapper'>
        <img class='me' src="/files/site_images/me.jpg">
    </div>
    <h1>Hi there!</h1>
    <p>
        My name is Cameron Casey, I'm an environment artist from all around the United States, currently working in the video game industry.
    </p>
    <p>
        Together with <a href="https://hdrihaven.com/" target="_blank">Greg Zaal</a>, I run the show here for 3D Model Haven.
    </p>

    <div style="clear: both"></div>

    <h1>About</h1>
    <p>
        3D Model Haven is a resource of free high quality 3D assets for everyone, following in the footsteps of <a href="https://hdrihaven.com/" target="_blank">HDRI Haven</a> and <a href="https://texturehaven.com/" target="_blank">Texture Haven</a>. Our goal is to create a constantly growing community-funded resource of open content, licensed as <a href="/p/license.php" target="_blank">CC0</a> for complete freedom and usability by professionals and hobbyists alike.
    </p>
    <p>
        All models here are <a href="/p/license.php">CC0</a> (public domain and copyright-free). No paywalls, accounts or email spam. Just download what you want, and use it however you like.
    </p>
    <p>
        3D Model Haven is officially linked with <a href="https://hdrihaven.com">HDRI Haven</a> and <a href="https://texturehaven.com">Texture Haven</a>.
    </p>

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
