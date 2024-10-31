<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<style>
    .date-picker-wrapper {
        border: none !important;
        background: none !important;
        padding: 0 !important;
    }
</style>

<div class="wrap">
<h2>Run Author Report</h2>

<p>Select the month:</p>

<?php
    $start = date("Y-m-d", strtotime("first day of this month"));
    $data = [];
    $posts = get_posts(['numberposts' => 120]);
    $currentYear = date("Y");
    foreach($posts as $row){
        if($currentYear !== date("Y", strtotime($row->post_date))){
            continue;
        }
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

<form id="report-form" method="post">
    <input type="hidden" name="run" value="author">
    <input type="hidden" id="report-result" name="report-result">

    <select name="date-range" id="date-range">
        <?php
            foreach(range(0,6) as $i) {
                $date = strtotime($start." -".$i." month");
                $month = date("F", $date);
                $datefull = date("Y-m-d", $date);
                echo "<option value='$datefull'>$month</option>";
            }
        ?>
    </select>
    <div id="date-range-container" style="width:456px;"></div>
    <?php wp_nonce_field( 'run-pulse-report' ); ?>
</form>

<p>
    <a class="button button-primary" id="report-start" style="cursor:pointer;">Start</a>
    <a class="button" href="<?php menu_page_url( 'pzsmb-reports-authors' ); ?>">Cancel</a>
</p>

<div id="report-loader" style="display:none;">
    <p>Running author report. This may take a few minutes.</p>
    <i class="fa fa-refresh fa-spin" style="font-size: 30px;margin-left:5px;margin-top:2px;"></i>
</div>
<div id="ajax-message"></div>

<script>
    var pulseData = JSON.parse('<?php echo addslashes(json_encode($data)); ?>');
    var reportType = 'author';
    var apiUrl = '<?php echo PAGEZII__SMB_PLUGIN_API; ?>';
    var blogUrl = '<?php echo get_site_url(); ?>';
</script>
