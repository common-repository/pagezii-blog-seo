<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<?php
    global $wpdb;
    $tablename = $wpdb->prefix . 'pagezii_smb_reports';
    $spotrows = $wpdb->get_results( "SELECT * FROM $tablename WHERE type='spot-pulse' ORDER BY time DESC" );
    $rows = $wpdb->get_results( "SELECT * FROM $tablename WHERE type='pulse' ORDER BY time DESC" );
?>

<div class="wrap">
<h2>Pulse Reports</h2>

<?php include('include-reports-menu.php'); ?>

<p><a href="<?php menu_page_url( 'pzsmb-dashboard' ); ?>&run=pulse" class="button button-primary">Run Spot Report</a></p>

<h3 style="margin-top: 30px;">Spot Pulse Reports</h3>

<table class="wp-list-table widefat fixed striped">
    <thead>
        <tr>
            <td>Report</td>
            <td>Date</td>
            <td>Posts Analyzed</td>
            <td>Avg. SEO</td>
            <td>Social Shares</td>
            <td>Reader Profile</td>
        </tr>
    </thead>
    <tbody>
        <?php
            $count = count($spotrows);
            $link = menu_page_url( 'pzsmb-reports-pulse', false );
            foreach($spotrows as $row){
                $info = $row->results2 ? json_decode($row->results2, true) : [];
                echo "<tr>";
                  echo "<td><a href='$link&id=$row->id&type=spot'>$count</a></td>";
                  echo "<td title='$row->time'>".date("F j, Y", strtotime($row->time))."</td>";
                  echo "<td>{$info['count']}</td>";
                  echo "<td>{$info['seoScore']}%</td>";
                  echo "<td>".number_format($info['shares'])."</td>";
                  echo "<td>{$info['readerProfile']}
                  <a href='{$link}&delete=$row->id' class='pz-table-row-delete'><i class='fa fa-times-circle'></i></a>
                  </td>";
                echo "</tr>";
                $count--;
            }
        ?>
    </tbody>
</table>

<?php
    if(count($spotrows) < 1) {
        echo "<p>None yet</p>";
    }
?>

<h3>Weekly Pulse Reports</h3>

<table class="wp-list-table widefat fixed striped">
    <thead>
        <tr>
            <td>Report</td>
            <td>Date</td>
            <td>Posts Analyzed</td>
            <td>Avg. SEO</td>
            <td>Social Shares</td>
            <td>Reader Profile</td>
        </tr>
    </thead>
    <tbody>
        <?php
            $count = count($rows);
            $link = menu_page_url( 'pzsmb-reports-pulse', false );
            foreach($rows as $row){
                $info = $row->results2 ? json_decode($row->results2, true) : [];
                echo "<tr>";
                  echo "<td><a href='$link&id=$row->id'>$count</a></td>";
                  echo "<td title='$row->time'>".date("F j, Y", strtotime($row->time))."</td>";
                  echo "<td>{$info['count']}</td>";
                  echo "<td>{$info['seoScore']}%</td>";
                  echo "<td>".number_format($info['shares'])."</td>";
                  echo "<td>{$info['readerProfile']}</td>";
                echo "</tr>";
                $count--;
            }
        ?>
    </tbody>
</table>
<?php
    if(count($rows) < 1) {
        echo "<p>None yet</p>";
    }
?>

</div>
