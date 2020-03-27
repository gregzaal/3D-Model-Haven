<?php
header('Content-type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
include ($_SERVER['DOCUMENT_ROOT'].'/php/functions.php');

// Main Pages
$urls = [
    ["https://3dmodelhaven.com/", "monthly"],
    ["https://3dmodelhaven.com/models/", "weekly"],
    ["https://3dmodelhaven.com/p/about-contact.php", "monthly"],
];
foreach ($urls as $u){
    echo "<url>";
    echo "<loc>".$u[0]."</loc>";
    echo "<changefreq>".$u[1]."</changefreq>";
    echo "<priority>0.5</priority>";
    echo "</url>";
}

// Models
$models = get_from_db();
foreach ($models as $t){
    echo "<url>";
    echo "<loc>https://3dmodelhaven.com/model/?m=".$t['slug']."</loc>";
    echo "<lastmod>".date("Y-m-d", strtotime($t['date_published']))."</lastmod>";
    echo "<priority>0.8</priority>";
    echo "<changefreq>monthly</changefreq>";
    echo "<image:image>";
    echo "<image:loc>https://3dmodelhaven.com/files/mod_images/renders/".$t['slug'].".jpg</image:loc>";
    echo "</image:image>";
    echo "</url>";
}

// Categories
$cats = get_all_categories();
foreach ($cats as $c){
    if ($c){
        if ($c != 'all'){
            echo "<url>";
            echo "<loc>https://3dmodelhaven.com/models/?c=".$c."</loc>";
            echo "<priority>0.5</priority>";
            echo "<changefreq>weekly</changefreq>";
            echo "</url>";
        }
    }
}

echo '</urlset>';
?>