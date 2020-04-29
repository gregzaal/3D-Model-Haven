<?php
include ($_SERVER['DOCUMENT_ROOT'].'/php/functions.php');
include_start_html("Finance Reports");
include ($_SERVER['DOCUMENT_ROOT'].'/php/html/header.php');
?>

<div id="page-wrapper">
    <h1>Finance Reports</h1>
    <p>
        <a href="https://docs.google.com/spreadsheets/d/1sgU7ozZmsdc_r_CpCJG2wCgxlnOHEl0fbhnxVejVP6E/edit?usp=sharing">Here's a spreadsheet</a> with all of our monthly expenses including upkeep costs, model acquisitions, personnel cuts, etc.
    </p>
    <p>If you have any questions, feel free to email me at <?php insert_email() ?></p>

</div>

<?php
include ($_SERVER['DOCUMENT_ROOT'].'/php/html/footer.php');
include ($_SERVER['DOCUMENT_ROOT'].'/php/html/end_html.php');
?>
