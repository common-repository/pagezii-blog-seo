<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<div class="wrap">
<h2>Run Pulse Report</h2>

<?php
    $data = [];
    $posts = get_posts(['numberposts' => 20]);
    foreach($posts as $row){
        $first_name = get_user_meta($row->post_author,'first_name',true);
        $last_name = get_user_meta($row->post_author,'last_name',true);
        $author = $first_name ? "$first_name $last_name": $row->post_author;
        $data[] = [
            'id' => $row->ID,
            'date' => $row->post_date,
            'author' => $author,
            'comments' => $row->comment_count,
        ];
    }
?>

<table class="wp-list-table widefat fixed striped posts" id="run-pulse-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Title</th>
            <th>Author</th>
            <th>Analyze</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $count = 0;
            foreach($posts as $key => $row) {
                $date = date("F j, Y g:i A", strtotime($row->post_date));
                $permalink = get_post_permalink($row->ID);
                $first_name = get_user_meta($row->post_author,'first_name',true);
                $last_name = get_user_meta($row->post_author,'last_name',true);
                $author = $first_name ? "$first_name $last_name": $row->post_author;
                echo "<tr>";
                  echo "<td>$date</td>";
                  echo "<td><a href='".$permalink."' target='_blank'>$row->post_title</td>";
                  echo "<td>$author</td>";
                  echo "<td><i class='fa fa-check-square-o' style='font-size:18px;' data-key='$count'></i></td>";
                echo "</tr>";
                $count++;
            }
        ?>
    </tbody>
</table>

<p>
    <a class="button button-primary" id="report-start" style="cursor:pointer;">Start</a>
    <a class="button" href="<?php menu_page_url( 'pzsmb-reports-pulse' ); ?>">Cancel</a>
</p>
<div id="report-loader" style="display:none;">
    <p>Running pulse report. This may take a few minutes.</p>
    <i class="fa fa-refresh fa-spin" style="font-size: 30px;margin-left:5px;margin-top:2px;"></i>
</div>
<div id="ajax-message"></div>
<form id="report-form" method="post">
    <input type="hidden" id="run" name="run" value="pulse">
    <input type="hidden" id="report-result" name="report-result">
    <?php wp_nonce_field( 'run-pulse-report' ); ?>
</form>

<script>
    var pulseData = JSON.parse('<?php echo addslashes(json_encode($data)); ?>');
    var reportType = 'pulse';
    var apiUrl = '<?php echo PAGEZII__SMB_PLUGIN_API; ?>';
    var blogUrl = '<?php echo get_site_url(); ?>';
</script>
