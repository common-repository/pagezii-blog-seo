<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<div class="wrap">
    <a class="button" href="<?php menu_page_url( 'pzsmb-reports-pulse' ); ?>">Back to Pulse Reports</a>
</div>
<div class="wrap" style="background: #fff; margin-top: 15px;">
    <div style="background-color: #35857D; color: #fff; padding: 2px 5px; margin-bottom: 10px; text-align: center;">
        <p style="display:inline-block; font-size: 20px; line-height: 8px;">Blog Pulse Report</p>
        <p style="display: inline-block; position: absolute; right: 35px; font-size: 15px;"><?php echo $date; ?></p>
    </div>
    <?php
        if(is_array($result) && count($result)){
            $profiles = $authors = $keywords = [];
            $shares = ['Facebook' => 0, 'Twitter' => 0, 'LinkedIn' => 0, 'Google+' => 0, 'Pinterest' => 0];
            $seoScore = 0;
            $countPosts = count($result);
            foreach($result as $i => $row){
                $post = get_post($row['id']);
                $result[$i]['post'] = $post;

                $first_name = get_user_meta($post->post_author,'first_name',true);
                $last_name = get_user_meta($post->post_author,'last_name',true);
                $author = $first_name ? "$first_name $last_name": $post->post_author;
                $result[$i]['author'] = $author;

                $result[$i]['permalink'] = get_post_permalink($post->ID);
                $result[$i]['title'] = $post->post_title;
                $result[$i]['date'] = $post->post_date;

                array_push($profiles, $row['readerProfile']);
                $shareCount = 0;
                foreach($row['shareCounts'] as $key => $value){
                    $shareCount += $value;
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
                $result[$i]['shares'] = $shareCount;
                if(!isset($authors[$author])){
                    $authors[$author] = [
                        'count' => 0,
                        'seoScore' => 0,
                        'readerProfiles' => [],
                        'shares' => 0
                    ];
                }
                $authors[$author]['count'] += 1;
                $authors[$author]['seoScore'] += $row['seoScore'];
                $authors[$author]['shares'] += $shareCount;
                $authors[$author]['readerProfiles'][] = $row['readerProfile'];
                $seoScore += $row['seoScore'];
                $words = $row['keywords'] ? explode(", ", $row['keywords']) : [];
                foreach($words as $word){
                    if(!isset($keywords[$word])){
                        $keywords[$word] = 0;
                    }
                    $keywords[$word] += (1.2*$row['comments']+1.5*$shareCount+1);
                }
            }
            $seoScore = round($seoScore/$countPosts);
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
            $minScore = min($keywords);
            $maxScore = max($keywords);
            $spread = $maxScore - $minScore;
            $minFontSize = 12;
            $maxFontSize = 30;
            $numKeywords = count($keywords);
            foreach($keywords as $key => $score){
                if($spread) {
                    $size = $minFontSize + ($score - $minScore) * ($maxFontSize - $minFontSize) / $spread;
                    $size = floor($size);
                    $keywords[$key] = ['size' => $size, 'score' => $score];
                } else {
                    $keywords[$key] = ['size' => $minFontSize, 'score' => $score];
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
        <div id="first-row" class="pagezii-report-body" style="margin-bottom: 35px;">
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
        <div id="second-row" class="pagezii-report-body" style="margin-bottom: 0px;">
            <h2>Author Snapshot</h2>
            <table class="wp-list-table widefat fixed striped posts">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Shares</th>
                        <th>Reader Profile</th>
                        <th>SEO Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach($authors as $key => $row) {
                            $rowProfiles = array_count_values($row['readerProfiles']);
                            $rowProfiles = array_keys($rowProfiles);
                            echo "<tr>";
                                echo "<td>$key</td>";
                                echo "<td>".number_format($row['shares'])."</td>";
                                echo "<td>".$rowProfiles[0]."</td>";
                                echo "<td>".round($row['seoScore']/$row['count'])."%</td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
        <div id="third-row" class="pagezii-report-body" style="margin-bottom: 15px;">
            <h2>Latest Post Analysis</h2>
            <table id="pz-posts-table" class="wp-list-table widefat fixed striped posts">
                <thead>
                    <tr>
                        <th>Title</th>
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
                        foreach($result as $key => $row) {
                            echo "<tr>";
                              echo "<td title='".$row['date']."'><a href='".$row['permalink']."' target='_blank'>{$row['title']}</td>";
                              echo "<td>".number_format($row['shares'])."</td>";
                              echo "<td>".number_format($row['comments'])."</td>";
                              echo "<td>{$row['readerProfile']}</td>";
                              echo "<td>{$row['pageLength']}</td>";
                              echo "<td>{$row['keywords']}</td>";
                              echo "<td>{$row['seoScore']}%</td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
        <div id="fourth-row" class="pagezii-report-body" style="margin-bottom: 15px;">
            <h2>Trending Blog Keywords</h2>
            <div class="tagcloud">
                <?php
                    foreach($keywords as $word => $value) {
                        echo "<span style='font-size:".$value['size']."px;'>$word</span>";
                    }
                ?>
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
            echo "<div class='pagezii-report-body' style='padding-top: 6px;'><p style='font-size: 16px;'>No results</p></div>";
        }
     ?>
    <div id="report-footer" class="pagezii-report-body">
        <p><a class="button button-primary" href="<?php menu_page_url( 'pzsmb-reports-pulse' ); ?>">Pulse Reports</a></p>
    </div>
</div>
