<?php
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    global $wpdb;
    $tablename = $wpdb->prefix . 'pagezii_smb_dashboard';
    $myrows = $wpdb->get_results( "SELECT * FROM $tablename" );
    $count = count($myrows);
    $row = $count ? $myrows[0] : null;
    $postsCount = $row ? $row->field3 : 0;
?>

<div class="wrap">
<h2>Dashboard</h2>

<?php include('include-reports-menu.php'); ?>

<?php
if($postsCount) {
    $data = $row->field2 ? json_decode($row->field2,true) : [];
    $seoScore = 0;
    $profiles = [];
    $shareCount = 0;
    $shares = ['Facebook' => 0, 'Twitter' => 0, 'LinkedIn' => 0, 'Google+' => 0, 'Pinterest' => 0];
    foreach($data as $i => $row){
        $id = $row['id'];
        $profile = $row['profile'];
        array_push($profiles, $profile);
        $shareCount += $row['sharesT'];
        $seoScore += $row['seo'];

        foreach($row['shares'] as $key => $value){
            if($key === 'FB'){
                $shares['Facebook'] += $value;
            } elseif ($key === 'TW') {
                $shares['Twitter'] += $value;
            } elseif ($key === 'LN') {
                $shares['LinkedIn'] += $value;
            } elseif ($key === 'Pinterest') {
                $shares['Pinterest'] += $value;
            } elseif ($key === 'Gplus') {
                $shares['Google+'] += $value;
            }
        }

        $post = get_post($id);
        $first_name = get_user_meta($post->post_author,'first_name',true);
        $last_name = get_user_meta($post->post_author,'last_name',true);
        $author = $first_name ? "$first_name $last_name": $post->post_author;
        $author = trim($author);

        $data[$i]['post'] = $post;
        $data[$i]['author'] = $author;
        $data[$i]['permalink'] = get_post_permalink($post->ID);
        $data[$i]['title'] = $post->post_title;
        $data[$i]['date'] = $post->post_date;
    }
    $seoScore = round($seoScore/$postsCount);
    $profiles = array_count_values($profiles);
    if(count($profiles) < 3){
        if(!isset($profiles['Artisan'])){
            $profiles['Artisan'] = 0;
        }
        if(!isset($profiles['Executive'])){
            $profiles['Executive'] = 0;
        }
        if(!isset($profiles['Communicator'])){
            $profiles['Communicator'] = 0;
        }
    }
?>
<style>
    #seo-pie::before {
        padding-top: 82px;
        font-size: 33px;
        display: inline-block;
        content: "<?php echo $seoScore; ?>%";
    }
    #seo-pie {
        border: 4px solid #55AD77;
        border-radius: 50%;
        width: 185px !important;
        height: 185px !important;
        padding: 0;
        margin: 0 auto;
    }
</style>
<div style="background: #fff; margin-top: 15px;">
<div id="first-row" class="pagezii-report-body" style="margin-bottom: 35px;">
    <div style="max-width: 1200px; margin: 0 auto; text-align:center;">
        <div style="display:inline-block; text-align: center; vertical-align: top;">
            <h2>Posts Analyzed</h2>
            <div style="height:30px; width:230px; font-size: 23px;">
                <?php echo $postsCount; ?>
            </div>
        </div>
        <div style="display:inline-block; text-align: center;">
            <h2>Common Reader Profile</h2>
            <div style="height:30px; width:230px; font-size: 23px;">
                <?php echo array_keys($profiles)[0]; ?>
            </div>
        </div>
        <div style="display:inline-block; text-align: center;">
            <h2>Social Shares</h2>
            <div style="height:30px; width:230px; font-size: 23px;">
                <?php echo number_format($shareCount); ?>
            </div>
        </div>
    </div>
</div>

<div id="second-row" class="pagezii-report-body" style="margin-bottom: 35px;">
    <div style="max-width: 1200px; margin: 0 auto; text-align:center;">
        <div style="display:inline-block; text-align: center; vertical-align: top;">
            <h2>SEO Score</h2>
            <div style="height:200px; width:230px; padding-top: 10px;">
                <div id="seo-pie" style="display:none; width:230px; height: 190px;" class="circle-pie"></div>
            </div>
        </div>
        <div style="display:inline-block; text-align: center;">
            <h2>Reader Profiles</h2>
            <div id="readers" style="height:200px; width:230px;"></div>
        </div>
        <div style="display:inline-block; text-align: center;">
            <h2>Social Shares</h2>
            <div id="shares" style="height:200px; width:230px;"></div>
        </div>
    </div>
</div>

<div id="third-row" class="pagezii-report-body" style="margin-bottom: 35px;">
    <h2>All Time Top 10 Posts</h2>
    <table id="pz-posts-table" class="wp-list-table widefat fixed striped pz-dashboard-posts">
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Shares</th>
                <th>Comments</th>
                <th>Reader Profile</th>
                <th>Page Length</th>
                <th>Keywords</th>
                <th>SEO Score</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $sorter = function($a,$b){
                    $valA = (1+$a['sharesT'])*0.01*$a['seo'];
                    $valB = (1+$b['sharesT'])*0.01*$b['seo'];
                    return $valA < $valB;
                };
                uasort($data,$sorter);
                $posts = array_slice($data,0,10);
                foreach($posts as $key => $row) {
                    echo "<tr>";
                      echo "<td title='".$row['date']."'><a href='".$row['permalink']."' target='_blank'>{$row['title']}</td>";
                      echo "<td>".$row['author']."</td>";
                      echo "<td>".number_format($row['sharesT'])."</td>";
                      echo "<td>".number_format($row['post']->comment_count)."</td>";
                      echo "<td>{$row['profile']}</td>";
                      echo "<td>{$row['length']}</td>";
                      echo "<td>{$row['kws']}</td>";
                      echo "<td>{$row['seo']}%</td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
</div>

</div>

<script>
    jQuery(document).ready(function($){
        var seoScore = "<?php echo $seoScore; ?>";
        var remaining = 100 - seoScore;
        var data = [{"label":"Something", "data": Math.round(seoScore)}, {"label": Math.round(remaining)+'%', "data": remaining}];
        $.plot('#seo-pie', data, {
            series: {
                pie: {
                    innerRadius: 0.73,
                    show: true,
                    label: false,
                    stroke: {
                        width: 0.1,
                        color: '#55AD77',
                    }
                }
            },
            colors: [ "#7AC598", "#ffffff"],
            legend: {
                show: false
            },
            grid: {
                hoverable: true,
            }
        });
        $("#seo-pie").fadeIn();

        $('#readers, #shares').html('');
        //GRAPH CODE
        var profiles = <?php echo json_encode($profiles); ?>;
        var shares = <?php echo json_encode($shares); ?>;
        var profDonut = [];
        var sharesDonut = [];
        var count=0;
        for (var prop in profiles) {
            profDonut[count] = [prop, profiles[prop]];
            count++;
        }
        var count=0;
        for (var prop in shares) {
            sharesDonut[count] = [prop, shares[prop]];
            count++;
        }
        var profDonutFormat = [];
        var sharesDonutFormat = [];
        for (var i = profDonut.length-1 ; i >= 0; i--) {
            var don = [];
            var lin = [];
            var linT = [];

            don["label"] = profDonut[i][0];
            don["value"] = profDonut[i][1];
            lin = [i, profDonut[i][1]];
            profDonutFormat.push(don);
        }
        for (var i = sharesDonut.length-1 ; i >= 0; i--) {
            var don = [];
            var lin = [];
            var linT = [];

            don["label"] = sharesDonut[i][0];
            don["value"] = sharesDonut[i][1];
            lin = [i, sharesDonut[i][1]];
            sharesDonutFormat.push(don);
        }
        var donutColours = ["#79C698","#EEBC7F","#9FACD3","#EBAC86","#92C2D6"];
        Morris.Donut({ element: 'readers', data: profDonutFormat , colors: donutColours });
        Morris.Donut({ element: 'shares', data: sharesDonutFormat , colors: donutColours });
    });
</script>


<?php
} else {
    echo "<p>No data yet.</p>";
}
?>

</div>
