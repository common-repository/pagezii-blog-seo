<?php
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    function pbs_getAuthorId($name){
        return preg_replace(['/[\s]+/','/[\'\.]/'],['-',''],$name);
    }
    if(isset($milestones)){
        $temp = $milestones['bounce'];
        $milestones['bounce'][0] = $temp[1];
        $milestones['bounce'][1] = $temp[0];
    }
    function pbs_milestoneTitle($key, $stats, $ranks, $milestones){
        if(!$ranks[$key]) {
            return '';
        }
        switch ($key) {
            case 'views':
                $title = 'Views this year: ' . number_format($stats['yearStats']['views']);
                break;
            case 'posts':
                $title = 'Posts this year: ' . number_format($stats['yearStats']['numPosts']);
                break;
            case 'shares':
                $title = 'Shares this year: ' . number_format($stats['yearStats']['shares']);
                break;
            case 'bounce':
                $title = 'Best Bounce Rate this year: ' . $stats['bestBounceRate'] . "%";
                break;
            case 'organic':
                $title = 'Organic views this year: ' . number_format($stats['yearStats']['organicViews']);
                break;
            case 'seo':
                $title = 'Best SEO Score this year: ' . $stats['bestSeoScore'] . "%";
                break;
        }
        if($ranks[$key] == 'rank1') {
            $next = number_format($milestones[$key][0]);
            $next = ($key == 'bounce' || $key == 'seo') ? "$next%" : $next;
            return $title . "<br>Next milestone: $next";
        } elseif ($ranks[$key] == 'rank2') {
            $next = number_format($milestones[$key][1]);
            $next = ($key == 'bounce' || $key == 'seo') ? "$next%" : $next;
            return $title . "<br>Next milestone: $next";
        } else {
            return $title;
        }
    }
?>

    <?php if(isset($authors) && !count($authors)) { ?>
        <div class="panel-blank clearfix">
            <div class="col-md-12 clearfix">
                <div class="alert alert-danger">
                    No authors found
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if(isset($authors) && count($authors)) { ?>
    <div class="panel-blank clearfix">
        <div class="col-sm-12 col-lg-12 mb20">
            <div class="panel-primary panel" id="awards">
                <div class="panel-header">
                    <h2>Authors of the Month</h2>
                </div>
                <?php
                    $awardInfo = [
                        ['pz-award-monthly-views.png', 'Best Seller','',' Read','The Best Seller author is the blogger with the most read blog posts that were published in the month.','future'],
                        ['pz-award-monthly-shares.png', 'Social Butterfly','',' Share','Social Butterfly is the blogger who generated the highest number of social shares per post in the month.',''],
                        ['pz-award-monthly-bounce.png', 'Riveting Writer','','% Bounce','Riveting Writer is the blogger who had the lowest monthly bounce rate.','future']
                    ];
                ?>
                <div class="panel-body panel-awards">
                    <?php if($awards) {
                        foreach($awards as $key => $value) {
                    ?>
                        <div class="col-awards col-lg-4 col-md-4 col-sm-4 col-xs-4" title="<?php echo $awardInfo[$key][4]; ?>">
                            <div>
                                <?php
                                if($awardInfo[$key][5] === 'future') {
                                    echo "<img src='".$imagesUrl . $awardInfo[$key][0] ."' style='opacity: 0.5'>";
                                    } else {
                                    echo "<img src='".$imagesUrl . $awardInfo[$key][0] ."' alt='" . $awardInfo[$key][1] . "'>";
                                } ?>
                            </div>
                            <div>
                              <?php
                                if($awardInfo[$key][5] === 'future') {
                                    echo "Coming Soon";
                                } else {
                                    echo $value['name']."<br>";
                                    if ($value['value'] != 1 && $key != 2) {
                                        echo $awardInfo[$key][2].number_format($value['value']).$awardInfo[$key][3]."s";
                                    } else {
                                        echo $awardInfo[$key][2].number_format($value['value']).$awardInfo[$key][3];
                                    }
                                }
                               ?>
                            </div>
                        </div>
                    <?php }
                    } else { ?>
                        <p style="padding-top: 20px;">No authors posted this month.</p>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-lg-12 mb20">
            <div class="panel-primary panel">
                <div class="panel-header">
                    <h2>Author Leaderboard Year-to-Date</h2>
                </div>
                <div class="panel-body paneltable table-responsive">
                    <table class="table table-hover" id="leaderboard-table">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Author</th>
                                <th># Posts</th>
                                <th>Views</th>
                                <th>Shares</th>
                                <th>Bounce</th>
                                <th>Reader Profile</th>
                                <th>SEO Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $rank=0;
                            $sorter = function($a,$b){
                                $valA = (1+$a['stats']['yearStats']['shares'])*0.01*$a['stats']['avgYearSeoScore'];
                                $valB = (1+$b['stats']['yearStats']['shares'])*0.01*$b['stats']['avgYearSeoScore'];
                                return $valA < $valB;
                            };
                            uasort($authors,$sorter);
                            foreach($authors as $key => $value) {
                                $rank++; $authors[$key]['rank'] = $rank; $authorId = pbs_getAuthorId($key); ?>
                                    <tr onclick="window.document.location='#author-<?php echo $authorId; ?>';">
                                        <td><?php echo $rank; ?></td>
                                        <td><?php echo $key; ?></td>
                                        <td><?php echo number_format($value['stats']['yearStats']['numPosts']); ?></td>
                                        <td>--</td>
                                        <td><?php echo number_format($value['stats']['yearStats']['shares']); ?></td>
                                        <td>--</td>
                                        <td><?php echo key($value['stats']['yearStats']['readerProfiles']); ?></td>
                                        <td><?php echo $value['stats']['avgYearSeoScore']; ?>%</td>
                                    </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php foreach($authors as $key => $value) {
                 $authorId = pbs_getAuthorId($key); ?>
            <div class="col-sm-12 col-lg-12 author-info" id="author-<?php echo $authorId; ?>">
                <div class="panel-primary panel">
                    <div class="panel-header">
                        <h2><span><?php echo $value['rank']; ?></span> <span><i class="badge"><?php echo $value['rank']; ?></i> <?php echo $key; ?></span></h2>
                    </div>
                    <div class="panel-body row">

                        <div class="col-lg-4 col-md-6 col-sm-6 mb20">
                            <p class="mb20">Monthly Metrics (<?php echo date("F Y", strtotime($dates[1]['date'])); ?>)</p>
                            <div class="blog-result-summary numbered-list">
                                <ul>
                                  <li class="colour-normal">
                                        <span class="result-label">Total Post Views</span> <span class="value">NA</span>
                                    </li>
                                    <li class="colour-normal">
                                        <span class="result-label">Total Organic Views</span> <span class="value">NA</span>
                                    </li>
                                    <li class="colour-normal">
                                        <span class="result-label">Published Posts</span> <span class="value"><?php echo number_format($value['stats']['monthStats']['numPosts']); ?></span>
                                    </li>
                                    <?php if($value['stats']['monthStats']['numPosts']) { ?>
                                        <li class="colour-normal">
                                            <span class="result-label">Avg. Shares per Post</span> <span class="value"><?php echo number_format(round($value['stats']['monthStats']['shares']/$value['stats']['monthStats']['numPosts'])); ?></span>
                                        </li>
                                        <li class="colour-normal">
                                            <span class="result-label">Avg. Views per Post</span> <span class="value">NA</span>
                                        </li>
                                        <li class="colour-normal">
                                            <span class="result-label">Avg. SEO Score</span> <span class="value"><?php echo round($value['stats']['monthStats']['seoScore']/$value['stats']['monthStats']['numPosts']); ?>%</span>
                                        </li>
                                    <?php } else { ?>
                                        <li class="colour-normal">
                                            <span class="result-label">Avg. Shares per Post</span> <span class="value">NA</span>
                                        </li>
                                        <li class="colour-normal">
                                            <span class="result-label">Avg. Views per Post</span> <span class="value">NA</span>
                                        </li>
                                        <li class="colour-normal">
                                            <span class="result-label">Avg. SEO Score</span> <span class="value">NA</span>
                                        </li>
                                    <?php } ?>
                                    <li class="colour-normal">
                                        <span class="result-label">Popular Reader Profile</span> <span class="value"><?php echo key($value['stats']['monthStats']['readerProfiles']) ?: 'NA'; ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-6 mb20">
                            <p class="mb20">Year to Date Metrics (<?php echo date("F", strtotime($dates[0]['date'])); ?> &mdash; <?php echo date("F Y", strtotime($dates[1]['date'])); ?>)</p>
                            <div class="blog-result-summary numbered-list padd-right-sm">
                                <ul>
                                  <li class="colour-normal">
                                        <span class="result-label">Total Post Views</span> <span class="value">NA</span>
                                    </li>
                                    <li class="colour-normal">
                                        <span class="result-label">Total Organic Views</span> <span class="value">NA</span>
                                    </li>
                                    <li class="colour-normal">
                                        <span class="result-label">Published Posts</span> <span class="value"><?php echo number_format($value['stats']['yearStats']['numPosts']); ?></span>
                                    </li>
                                    <li class="colour-normal">
                                        <span class="result-label">Avg. Shares per Post</span> <span class="value"><?php echo number_format(round($value['stats']['yearStats']['shares']/$value['stats']['yearStats']['numPosts'])); ?></span>
                                    </li>
                                    <li class="colour-normal">
                                        <span class="result-label">Avg. Views per Post</span> <span class="value">NA</span>
                                    </li>
                                    <li class="colour-normal">
                                        <span class="result-label">Avg. SEO Score</span> <span class="value"><?php echo round($value['stats']['yearStats']['seoScore']/$value['stats']['yearStats']['numPosts']); ?>%</span>
                                    </li>
                                    <li class="colour-normal">
                                        <span class="result-label">Popular Reader Profile</span> <span class="value"><?php echo key($value['stats']['yearStats']['readerProfiles']) ?: 'NA'; ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-12 col-sm-12 mb20 milestones">
                            <div class="milestone-icons">
                                <div class="col-md-4 col-sm-4 col-xs-4 milestone-hover" title="Coming soon">
                                    <img src="<?php echo $imagesUrl . 'views.svg'; ?>" class="">
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-4 milestone-hover" title="<?php echo pbs_milestoneTitle('posts', $value['stats'], $value['ranks'], $milestones); ?>">
                                    <img src="<?php echo $imagesUrl . 'posts.svg'; ?>" class="<?php echo $value['ranks']['posts']; ?>">
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-4 milestone-hover" title="<?php echo pbs_milestoneTitle('shares', $value['stats'], $value['ranks'], $milestones); ?>">
                                    <img src="<?php echo $imagesUrl . 'shares.svg'; ?>" class="<?php echo $value['ranks']['shares']; ?>">
                                </div>

                                <div class="col-md-4 col-sm-4 col-xs-4 milestone-hover" title="Coming soon">
                                    <img src="<?php echo $imagesUrl . 'bounce.svg'; ?>" class="">
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-4 milestone-hover" title="Coming soon">
                                    <img src="<?php echo $imagesUrl . 'organic.svg'; ?>" class="">
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-4 milestone-hover" title="<?php echo pbs_milestoneTitle('seo', $value['stats'], $value['ranks'], $milestones); ?>">
                                    <img src="<?php echo $imagesUrl . 'seo.svg'; ?>" class="<?php echo $value['ranks']['seo']; ?> seoScore" data-value='<?php echo $value['stats']['bestSeoScore']; ?>'>
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="panel-header">
                        <h2>Most Popular Posts</h2>
                    </div>
                    <div class="table-responsive panel-body paneltable">
                        <table class="table table-hover author-table">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Post URL</th>
                                    <th>Views</th>
                                    <th>Shares</th>
                                    <th>Bounce</th>
                                    <th>Reader Profile</th>
                                    <th>SEO Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $rank=0;
                                foreach(array_slice($value['posts'],0,10) as $post) {
                                    $rank++; ?>
                                    <tr>
                                        <td><?php echo $rank; ?></td>
                                        <td><a href="<?php echo $post['url']; ?>"><?php echo $post['path']; ?></a></td>
                                        <td>--</td>
                                        <td><?php echo number_format($post['shares']); ?></td>
                                        <td>--</td>
                                        <td><?php echo $post['readerProfile']; ?></td>
                                        <td><?php echo round($post['seoScore']); ?>%</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mb20" style="height: 60px;">
                <a class="pull-right toplink" href="#">
                    <i class="fa fa-chevron-up"></i>
                </a>
            </div>
        <?php } ?>

    </div>
    <?php } ?>
