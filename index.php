<?php
include ($_SERVER['DOCUMENT_ROOT'].'/php/functions.php');
include_start_html("3D Model Haven");
include ($_SERVER['DOCUMENT_ROOT'].'/php/html/header.php');
?>

<div id='landing-banner-wrapper'>
    <div id='banner-img-a' style='background-position: center bottom'>
        <div class='banner-img-credit'>Render by Greg Zaal</div>
    </div>
    <!-- <div id='banner-img-b' class='hide'>
        <div class='banner-img-credit'></a></div>
    </div>
    <div id='banner-img-paddle-l' class='banner-img-paddle'><i class="material-icons">keyboard_arrow_left</i></div>
    <div id='banner-img-paddle-r' class='banner-img-paddle'><i class="material-icons">keyboard_arrow_right</i></div> -->


    <div id='banner-title-wrapper'>
        <img src="/core/img/Model Haven Logo.svg" id="banner-logo" />
        <p>100% Free 3D Models, for Everyone.</p>
    </div>
</div>

<div id='landing-page'>

    <div class="segment-b">
        <div class="segment-inner">
            <div class="col-2">
                <h1>100% Free</h1>
                <p>All models are licenced as <b>CC0</b> and can be downloaded instantly, giving you complete freedom.</p>
                <p>No paywalls, email forms or account systems.</p>
                <a href="/p/license.php">
                    <div class='button'>Read More</div>
                </a>
            </div>

            <div class="col-2">
                <h1>Multi-format PBR</h1>
                <p>High & low poly, 4k PBR textures, multiple software formats...</p>
                <p>All the background assets you'll need to build the next AAA game, film, or visualization - no matter your tool of choice.</p>
                <a href="/models">
                    <div class='button'>Browse Models</div>
                </a>
            </div>
        </div>
    </div>

    <div class="segment-a">
        <div class="segment-inner">

            <h1>Supported by you<img src="/files/site_images/icons/heart.svg" class='heart'></h1>
            <div class="col-2">
                <h2 class="patreon-stat" id="patreon-num-patrons"><?php echo sizeof($GLOBALS['PATRON_LIST']) ?> patrons</h2>
            </div>
            <div class="col-2">
                <h2 class="patreon-stat" id="patreon-income">$<?php echo $GLOBALS['PATREON_EARNINGS'] ?> per month</h2>
            </div>

            <div class='patreon-bar-wrapper'>
                <div class="patreon-bar-outer">
                    <div class="patreon-bar-inner-wrapper">
                        <div class="patreon-bar-inner" style="width: <?php echo $GLOBALS['PATREON_CURRENT_GOAL']['completed_percentage'] ?>%"></div>
                    </div>
                </div>
                <div class="patreon-current-goal">Current goal: <b><?php
                    echo goal_title($GLOBALS['PATREON_CURRENT_GOAL']);
                    echo " ($";
                    echo $GLOBALS['PATREON_CURRENT_GOAL']['amount_cents']/100;
                    echo ")";
                ?></b><i class="material-icons hide-mobile">arrow_upward</i></div>
            </div>

            <div class="text-block">
                <p>As a 3D artist you know how much work it takes to build high quality 3D models - especially ones that thousands of people might use for a variety of purposes.</p>
                <p>Rather than sell our 3D assets, we provide them for free to everyone without restriction. The goal of 3D Model Haven, along with the other Havens, is to both <b>raise the bar</b> for the quality of work that artists like you produce, as well as <b>lower the barrier to entry</b> for newer artists.</p>
                <p>If you'd like to help us work towards this goal, we would very much appreciate your support. The allocation and spendings of your donations can be verified by monthly <a href="/p/finance-reports.php" target="_blank">public finance reports</a>.</p>
            </div>

            <a href="https://polyhaven.com/support-us" target="_blank">
                <div class='button-inline'>Read More / Become a Patron<img src="/files/site_images/icons/heart_white.svg" class='heart-inline'></div>
            </a>
        </div>
    </div>

    <?php
    $conn = db_conn_read_only();
    $comm_sponsors = get_commercial_sponsors($conn);
    if (!empty($comm_sponsors)){
        echo "<div class='segment-a'>";
        echo "<div class='segment-inner'>";
        echo "<h2>Also supported by:</h2>";
        echo "<div class='commercial_sponsors'>";
        foreach ($comm_sponsors as $s){
            echo "<a href= \"".$s['link']."\" target='_blank'>";
            echo "<img src=\"/files/site_images/commercial_sponsors/";
            echo $s['logo'];
            echo "\" alt=\"";
            echo $s['name'];
            echo "\" title=\"";
            echo $s['name'];
            echo "\"/>";
            echo "</a>";
        }
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
    ?>

    <div class="segment-montage">
        <div class="segment-montage-hover"></div>
        <a href="/models">
            <div class='button'>Browse Models</div>
        </a>
    </div>

    <div class="segment-a">
        <div class="segment-inner segment-about">
            <h1>About</h1>
            <img class='me' src="/files/site_images/me.jpg">
            <p>
                Hi there! My name is Greg Zaal, and I'm a CG artist and Open Source advocate.
            </p>
            <p>
                Our goal for 3D Model Haven is to create a constantly growing community-funded resource of <b>open content</b>, for complete freedom and usability by professionals and hobbyists alike.
            </p>
            <p>
                All models on this site are free, no catch, released as <a href="/p/license.php">CC0</a> (public domain and copyright-free). No paywalls, accounts or email spam. Just download what you want, and use it for any purpose.
            </p>
            <p>
                If you like what we do and want to keep this site alive, consider <a href="https://polyhaven.com/support-us">supporting us on Patreon</a>.
            </p>
        </div>
        <div style="clear: both"></div>
    </div>

</div>  <!-- #landing-page -->

<?php
include ($_SERVER['DOCUMENT_ROOT'].'/php/html/footer.php');
include ($_SERVER['DOCUMENT_ROOT'].'/php/html/end_html.php');
?>
