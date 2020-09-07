<?php
include ($_SERVER['DOCUMENT_ROOT'].'/php/functions.php');
include_start_html("Finance Reports");
include ($_SERVER['DOCUMENT_ROOT'].'/php/html/header.php');

$conn = db_conn_read_only();

$sql = "SELECT sum(amount_c)/100 FROM savings WHERE type=\"model_fund\"";
$balance_model_fund = array_values(mysqli_fetch_assoc(mysqli_query($conn, $sql)))[0];
$sql = "SELECT sum(amount_c)/100 FROM savings WHERE type=\"equipment\"";
$balance_equipment = array_values(mysqli_fetch_assoc(mysqli_query($conn, $sql)))[0];
?>

<div id="page-wrapper">
    <h1>Finance Reports</h1>
    <p>
        All Patreon donations go directly towards maintenance of the site or acquisition of new assets.
    </p>
    <p>
        All spendings, savings and allocations of the income each month is detailed in the public spreadsheets below.
    </p>
    <p>
        In a nutshell:
    <ul>
        <li>Running costs are deducted first (server fees, necessary software licenses, etc).</li>
        <li>Staff is paid a small portion for their time spent keeping the wheels turning (curation, communication, patron reward fulfilment, server maintenance...).</li>
        <li>The remainder (still the majority of the original donations) is assigned to the Asset Fund, to be used for asset acquisition as shown in the table below.</li>
    </ul>
    </p>
    <p>
        If you have any questions, feel free to email me at <?php insert_email() ?>.
    </p>

    <div class='col-2' style='vertical-align: top'>
    <h2>Detailed Monthly Reports</h2>
    <p style="font-size: 1em">
    <?php
    $sql = "SELECT * FROM `finance_reports` ORDER BY `datetime` desc";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $link = $row['link'];
            $month = date("F Y", strtotime($row['datetime']));
            echo "<a href=\"{$link}\">";
            echo "<span class='button-inverse-small'>{$month}</span>";
            echo "</a>";
        }
    }
    ?>
    </p>

    <p>
        For a quick visual overview of how 3D Model Haven has grown over time, I recommend checking out the <a href="https://graphtreon.com/creator/3dmodelhaven">Graphtreon page</a>.
    </p>
    </div>

    <?php
    $sql = "SELECT * FROM `savings` WHERE type = \"model_fund\" ORDER BY `datetime` desc";
    $result = mysqli_query($conn, $sql);
    $model_fund = array();
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $model_fund[$row['id']] = $row;
        }
    }
    ?>

    <div class='col-2' style='vertical-align: top'>
    <h2>Asset Fund</h2>
    <p>Current balance: <b class='<?php echo ($balance_model_fund>0?"green":"red"); ?>-text'>R<?php echo fmoney($balance_model_fund); ?></b> (<a href="https://www.google.co.za/search?q=<?php echo abs($balance_model_fund) ?>+zar+in+usd" target="_blank">ZAR</a>)</p>

    <div class='savings-tables'>
    <table cellspacing=0>
        <tr>
            <th>Date</th>
            <th>Description</th>
            <th>Amount</th>
        </tr>
        <?php
        foreach($model_fund as $x){
            echo "<tr>";
            echo "<td>".date("Y-m-d", strtotime($x['datetime']))."</td>";
            echo "<td>";
            if ($x['link']){
                echo "<a href=\"{$x['link']}\">";
                echo $x['description'];
                echo "</a>";
            }else{
                echo $x['description'];
            }
            echo "</td>";
            echo "<td class='".($x['amount_c']>0?"green":"red")."-text'>";
            echo "R".fmoney($x['amount_c']/100);
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>
    </div>
    </div>

</div>

<?php
include ($_SERVER['DOCUMENT_ROOT'].'/php/html/footer.php');
include ($_SERVER['DOCUMENT_ROOT'].'/php/html/end_html.php');
?>
