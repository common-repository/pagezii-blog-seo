<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<div class="wrap">
    <a class="button" href="<?php menu_page_url( 'pzsmb-reports-authors' ); ?>">Back to Author Reports</a>
</div>

<div class="wrap" style="background: #fff; margin-top: 15px;">
    <div style="background-color: #35857D; color: #fff; padding: 2px 5px; margin-bottom: 10px; text-align: center;">
        <p style="display:inline-block; font-size: 20px; line-height: 8px;">Blog Author Report</p>
        <p style="display: inline-block; position: absolute; right: 35px; font-size: 15px;"><?php echo $date; ?></p>
    </div>
    <?php
        if(is_array($result) && count($result)){
            $authors = $result['data']['authors'];
            $milestones = $result['milestones'];
            $awards = $result['awards'];
            $dates = $result['dates'];
            echo "<div id='second-row'>";
            include('include-author-report.php');
            echo "</div>";
    ?>


        <script>
            jQuery(document).ready(function($){
                function updateText(dom, selector1, selector2){
                    node = dom.find(selector1);
                    text = node.find(selector2);
                    value = node.attr('data-value');
                    if(value !== 'NA'){
                        text.html(value+'%');
                        if(value >= 100){
                            text.attr('x',53);
                        }
                        if(value < 10){
                            text.attr('x',62);
                        }
                    }
                }
                var svgs = $('.milestone-icons > div > img');
                SVGInjector(svgs,'',function(a){
                    $('.author-info').each(function(){
                        updateText($(this), '.bounceRate', '.bounce-number');
                        updateText($(this), '.seoScore', '.seo-number');
                    });
                });
                $(".milestone-hover").tooltip({
                    'placement': 'top',
                    'html': true,
                });
                $(".panel-awards .col-awards").tooltip({
                    'placement': 'top',
                    'container': '.panel-awards'
                });
                $(".milestone-hover, .panel-awards .col-awards").click(function(){
                    $(this).tooltip('toggle');
                });
            });
        </script>
    <?php
        } else {
            echo "<div class='pagezii-report-body' style='padding-top: 6px;'><p style='font-size: 16px;'>No results</p></div>";
        }
     ?>
    <div id="report-footer" class="pagezii-report-body">
        <p><a class="button button-primary" href="<?php menu_page_url( 'pzsmb-reports-authors' ); ?>">Author Reports</a></p>
    </div>
</div>
