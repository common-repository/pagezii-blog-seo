<?php
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    global $wpdb;
    $tablename = $wpdb->prefix . 'pagezii_smb_reports';
    $spotrows = $wpdb->get_results( "SELECT * FROM $tablename WHERE type='spot-author' ORDER BY time DESC" );
    $rows = $wpdb->get_results( "SELECT * FROM $tablename WHERE type='author' ORDER BY time DESC" );
?>

<div class="wrap">
<h2>Author Reports</h2>

<?php include('include-reports-menu.php'); ?>

<p><a href="<?php menu_page_url( 'pzsmb-dashboard' ); ?>&run=author" class="button button-primary">Run Spot Report</a></p>

<h3 style="margin-top: 30px;">Spot Author Reports</h3>

<table class="wp-list-table widefat fixed striped">
    <thead>
        <tr>
            <td>Report</td>
            <td>Report Month</td>
            <td>Posts in Month</td>
            <td>Total Shares</td>
            <td>Social Author</td>
            <td>Reader Profile</td>
        </tr>
    </thead>
    <tbody>
        <?php
            $count = count($spotrows);
            $link = menu_page_url( 'pzsmb-reports-authors', false );
            foreach($spotrows as $row){
                $info = $row->results2 ? json_decode($row->results2, true) : [];
                if(isset($info['authorData'])){
                    $readerProfiles = $info['authorData']['stats']['yearStats']['readerProfiles'];
                    $readerProfile = array_keys($readerProfiles)[0];
                } else {
                    $readerProfile = '';
                }
                echo "<tr>";
                  echo "<td><a href='$link&id=$row->id&type=spot'>$count</a></td>";
                  echo "<td title='Analyzed at: $row->time'>".date("F Y", strtotime($row->info))."</td>";
                  echo "<td>".$info['count']."</td>";
                  echo "<td>".number_format(isset($info['shares']) ? $info['shares'] : 0)."</td>";
                  echo "<td>".$info['topAuthor']."</td>";
                  echo "<td>$readerProfile
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

<h3>Monthly Author Reports</h3>

<table class="wp-list-table widefat fixed striped">
    <thead>
        <tr>
            <td>Report</td>
            <td>Report Month</td>
            <td>Posts in Month</td>
            <td>Total Shares</td>
            <td>Social Author</td>
            <td>Reader Profile</td>
        </tr>
    </thead>
    <tbody>
        <?php
            $count = count($rows);
            $link = menu_page_url( 'pzsmb-reports-authors', false );
            foreach($rows as $row){
                $info = $row->results2 ? json_decode($row->results2, true) : [];
                if(isset($info['authorData'])){
                    $readerProfiles = $info['authorData']['stats']['yearStats']['readerProfiles'];
                    $readerProfile = array_keys($readerProfiles)[0];
                } else {
                    $readerProfile = '';
                }
                echo "<tr>";
                  echo "<td><a href='$link&id=$row->id&type=spot'>$count</a></td>";
                  echo "<td title='Analyzed at: $row->time'>".date("F Y", strtotime($row->info))."</td>";
                  echo "<td>".$info['count']."</td>";
                  echo "<td>".number_format(isset($info['shares']) ? $info['shares'] : 0)."</td>";
                  echo "<td>".$info['topAuthor']."</td>";
                  echo "<td>$readerProfile</td>";
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
