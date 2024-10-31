<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pagezii_SMB_Admin {

    private static $initiated = false;

    public static function init() {
        self::init_hooks();
    }

    public static function init_hooks() {
        self::$initiated = true;
        add_action( 'admin_menu', array( 'Pagezii_SMB_Admin', 'my_plugin_menu') );
        add_action( 'admin_enqueue_scripts', array( 'Pagezii_SMB_Admin', 'load_resources' ) );
    }

    public static function load_resources(){
        wp_enqueue_script( 'pagezii-smb-scripts', plugins_url( 'js/scripts.js', __FILE__ ), array( 'jquery' ), PAGEZII_SMB_VERSION, true );
        wp_enqueue_style( 'fontawesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), PAGEZII_SMB_VERSION );
        wp_register_style( 'pagezii.css', plugin_dir_url( __FILE__ ) . 'css/pagezii.css', array(), PAGEZII_SMB_VERSION );
        wp_enqueue_style( 'pagezii.css');
    }

    public static function load_report_resources()
    {
        wp_enqueue_script( 'raphael', plugins_url( 'js/raphael-2.1.0.min.js', __FILE__ ), array( 'jquery' ), PAGEZII_SMB_VERSION, true );
        wp_enqueue_script( 'morrisjs', plugins_url( 'js/morris.js', __FILE__ ), array( 'jquery' ), PAGEZII_SMB_VERSION, true );
        wp_enqueue_style( 'morriscss', plugins_url( 'css/morris.css', __FILE__ ), array(), PAGEZII_SMB_VERSION );
        wp_enqueue_script( 'jqueryflot', plugins_url( 'js/jquery.flot.min.js', __FILE__ ), array( 'jquery' ), PAGEZII_SMB_VERSION, true );
        wp_enqueue_script( 'jqueryflotpie', plugins_url( 'js/jquery.flot.pie.min.js', __FILE__ ), array( 'jquery' ), PAGEZII_SMB_VERSION, true );
    }

    public static function load_author_report_resources()
    {
        wp_enqueue_script( 'bootstraptooltip', plugins_url( 'js/tooltip.js', __FILE__ ), array( 'jquery' ), PAGEZII_SMB_VERSION, true );
        wp_enqueue_style( 'bootstraptooltip', plugins_url( 'css/tooltip.css', __FILE__ ), array(), PAGEZII_SMB_VERSION );
        wp_enqueue_script( 'pz-svg', plugins_url( 'js/svg-injector.min.js', __FILE__ ), array(), PAGEZII_SMB_VERSION, true );
        wp_enqueue_style( 'pz-svg-css', plugins_url( 'css/svg-milestone-icons.css', __FILE__ ), array(), PAGEZII_SMB_VERSION );
        wp_enqueue_style( 'pz-author', plugins_url( 'css/author.css', __FILE__ ), array(), PAGEZII_SMB_VERSION );
    }

    public static function load_date_picker_resources()
    {
        wp_enqueue_script( 'momentjs', plugins_url( 'js/moment.min.js', __FILE__ ), array( 'jquery' ), PAGEZII_SMB_VERSION, true );
        wp_enqueue_script( 'datepickerjs', plugins_url( 'js/jquery.daterangepicker.min.js', __FILE__ ), array( 'jquery' ), PAGEZII_SMB_VERSION, true );
        wp_enqueue_style( 'datepickercss', plugins_url( 'css/daterangepicker.min.css', __FILE__ ), array(), PAGEZII_SMB_VERSION );
    }

    public static function my_plugin_menu() {
        $capabilities = 'manage_options';
        $icon_url =  plugin_dir_url( __FILE__ ). 'images/logo-small.png';
        $position = null;

        add_menu_page( 'Dashboard', 'Pagezii Blog SEO', $capabilities, 'pzsmb-dashboard', array( 'Pagezii_SMB_Admin', 'dashboard_menu'), $icon_url, $position );
        add_submenu_page( 'pzsmb-dashboard', 'Pulse Reports', 'Pulse Reports', $capabilities, 'pzsmb-reports-pulse', array( 'Pagezii_SMB_Admin', 'reports_pulse_menu') );
        add_submenu_page( 'pzsmb-dashboard', 'Author Reports', 'Author Reports', $capabilities, 'pzsmb-reports-authors', array( 'Pagezii_SMB_Admin', 'reports_author_menu') );
        add_submenu_page( 'pzsmb-dashboard', 'Setup', 'Setup', $capabilities, 'pzsmb-main', array( 'Pagezii_SMB_Admin', 'setup_menu') );
        global $submenu;
        if ( current_user_can( 'manage_options' ) )  {
            $submenu['pzsmb-dashboard'][0][0] = __( 'Dashboard', 'pzsmb-dashboard' );
        }
    }

    public static function setup_menu() {
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        if($_POST){
            check_admin_referer( 'save-general-settings' );
            $pz_settings = self::update_pz_settings('main');
            if ( wp_next_scheduled( 'pagezii_smb_pulse_job' ) ) {
                wp_clear_scheduled_hook( 'pagezii_smb_pulse_job' );
            }
            if ( wp_next_scheduled( 'pagezii_smb_author_job' ) ) {
                wp_clear_scheduled_hook( 'pagezii_smb_author_job' );
            }
            $reportDay = $pz_settings['report_day'];
            $days = [
                '1'=>'Monday','2'=>'Tuesday','3'=>'Wednesday',
                '4'=>'Thursday','5'=>'Friday','6'=>'Saturday','7'=>'Sunday'
            ];
            if(isset($days[$reportDay])){
                $dayOfWeek = $days[$reportDay];
            } else {
                $dayOfWeek = 'Monday';
            }
            if($pz_settings['pulse_reports'] && $pz_settings['to_email']){
                wp_schedule_event( strtotime("Next $dayOfWeek"), 'weekly', 'pagezii_smb_pulse_job' );
            }

            if($pz_settings['author_reports'] && $pz_settings['to_email']){
                wp_schedule_event( strtotime("First day of next month"), 'monthly', 'pagezii_smb_author_job' );
            }
        } else {
            $pz_settings = self::get_pz_settings();
        }
        require_once( PAGEZII__SMB_PLUGIN_DIR . 'views/setup.php' );
    }

    public static function dashboard_menu() {
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        if($_POST){
            check_admin_referer( 'run-pulse-report' );
            if(isset($_POST['report-result']) && isset($_POST['run'])){
                $result = stripslashes($_POST['report-result']);
                $result = json_decode($result,true);
                if(!$result){
                    echo "<p>Error retrieving results. Please try again or contact support.</p>"; die;
                }
                if($_POST['run'] === 'pulse') {
                    $response = self::savePulseData($result,'spot-pulse');
                    $next = $response['link'];
                    wp_redirect($next);
                } elseif($_POST['run'] === 'author') {
                    $response = self::saveAuthorData($result,'spot-author');
                    $next = $response['link'];
                    wp_redirect($next);
                }
            }
        } else {
            if(isset($_GET['run'])) {
                if ($_GET['run'] === 'pulse') {
                    require_once( PAGEZII__SMB_PLUGIN_DIR . 'views/run-pulse-report.php' );
                } elseif ($_GET['run'] === 'author') {
                    require_once( PAGEZII__SMB_PLUGIN_DIR . 'views/run-author-report.php' );
                }
            } else {
                self::load_report_resources();
                require_once( PAGEZII__SMB_PLUGIN_DIR . 'views/dashboard.php' );
            }
        }
    }

    public static function savePulseData($result, $type)
    {
        $rows = [];
        foreach($result as $row){
            $rows[] = [
                'id' => $row['id'],
                'comments' => $row['comments'],
                'seoScore' => $row['seoScore'],
                'keywords' => $row['keywords'],
                'readerProfile' => $row['readerProfile'],
                'shareCounts' => $row['shareCounts'],
                'pageLength' => $row['pageLength'],
                'links' => $row['links'],
                'images' => $row['images'],
            ];
        }
        $count = count($rows);
        $info = $count;
        $summary = self::summarizePulseReport($rows);
        $id = self::saveReportResult($rows, $info, $summary, $type, $summary);
        self::updateDashboardData($rows);

        $blogUrl = get_admin_url() . 'admin.php?page=pzsmb-reports-pulse';
        $link = menu_page_url( 'pzsmb-reports-pulse', false ) ?: $blogUrl;
        if ($type == 'spot-pulse') {
            $link = $link . "&id=$id&type=spot";
        } else {
            $link = $link . "&id=$id";
        }
        return ['id' => $id, 'link' => $link];
    }

    public static function saveAuthorData($result, $type)
    {
        $count = 0;
        $rows = $result;
        $info = $rows['dates'][1]['date'];
        $summary = self::summarizeAuthorReport($rows);
        $id = self::saveReportResult($rows, $info, $summary, $type);

        $blogUrl = get_admin_url() . 'admin.php?page=pzsmb-reports-authors';
        $link = menu_page_url( 'pzsmb-reports-authors', false ) ?: $blogUrl;
        if ($type == 'spot-author') {
            $link =  $link . "&id=$id&type=spot";
        } else {
            $link =  $link . "&id=$id";
        }
        return ['id' => $id, 'link' => $link];
    }

    public static function summarizePulseReport($rows)
    {
        $countPosts = count($rows);
        if(!$countPosts > 0){
            return [];
        }
        $profiles = [];
        $shareCount = 0;
        $seoScore = 0;
        foreach($rows as $i => $row){
            array_push($profiles, $row['readerProfile']);
            foreach($row['shareCounts'] as $key => $value){
                $shareCount += $value;
            }
            $seoScore += $row['seoScore'];
        }
        $seoScore = round($seoScore/$countPosts);
        $profiles = array_count_values($profiles);
        $profile = array_keys($profiles)[0];
        $data = ['count'=>$countPosts,'shares'=>$shareCount,'seoScore'=>$seoScore,'readerProfile'=>$profile];
        return $data;
    }

    public static function summarizeAuthorReport($rows)
    {
        $authors = $rows['data']['authors'];
        $countAuthors = count($authors);
        if(!$countAuthors > 0){
            return [];
        }
        $countPosts = 0;
        $countShares = 0;
        $authorData = null;
        $name = $rows['awards'][1]['name'];
        foreach($authors as $key => $author){
            $countPosts += $author['stats']['monthStats']['numPosts'];
            $countShares += $author['stats']['monthStats']['shares'];
            if($key === $name){
                $authorData = $author;
            }
        }

        return ['count'=>$countPosts,'shares'=>$countShares,'topAuthor'=>$name,'authorData'=>$authorData];
    }

    public static function updateDashboardData($posts)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'pagezii_smb_dashboard';
        $row = $wpdb->get_results( "SELECT * FROM $table_name WHERE id='1'" );
        $row_posts = $row ? json_decode($row[0]->field2, true) : [];
        $row_posts = $row_posts ?: [];
        foreach($posts as $row){
            $id = $row['id'];
            $value = [
                'id' => $row['id'],
                'seo' => $row['seoScore'],
                'kws' => $row['keywords'],
                'profile' => $row['readerProfile'],
                'shares' => $row['shareCounts'],
                'sharesT' => $row['shareCounts']['FB']
                                + $row['shareCounts']['TW']
                                + $row['shareCounts']['LN']
                                + $row['shareCounts']['Pinterest']
                                + $row['shareCounts']['Gplus'],
                'length' => $row['pageLength'],
                'links' => $row['links'],
                'images' => $row['images'],
                'updated' => date("Y-m-d h:i:s A")
            ];
            $row_posts[$id] = $value;
        }
        $count = count($row_posts);

        $wpdb->update(
            $table_name,
            array(
                'field2' => json_encode($row_posts),
                'field3' => $count
            ),
            array( 'ID' => 1 ),
            array(
                '%s',    // value1
                '%d',
            ),
            array( '%d' )
        );

        return;
    }

    public static function saveReportResult($data,$info,$summary,$type)
    {
        $results = json_encode($data);
        $summary = json_encode($summary);
        if(!$results){
            echo "Error saving data. Please try running the report again.";
            die;
        }
        global $wpdb;
        $table_name = $wpdb->prefix . 'pagezii_smb_reports';
        $row = $wpdb->insert(
            $table_name,
            array(
                'time' => current_time( 'mysql' ),
                'type' => (string) trim($type),
                'results' => (string) $results,
                'results2' => (string) $summary,
                'info' => (string) sanitize_text_field($info),
            )
        );
        return $wpdb->insert_id;
    }

    public static function get_report_row($id, $type='pulse')
    {
        global $wpdb;
        $tablename = $wpdb->prefix . 'pagezii_smb_reports';
        $row = $wpdb->get_results( "SELECT * FROM $tablename WHERE type='".$type."' AND id=".$id );
        return $row;
    }

    public static function reports_pulse_menu() {
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        require_once( PAGEZII__SMB_PLUGIN_DIR . 'class.pagezii-main.php' );

        $next = menu_page_url( 'pzsmb-reports-pulse', false ) ;

        if($_GET['id'] !== null){
            if($_GET['id'] === ''){
                wp_redirect($next);
            }
            self::load_report_resources();

            if($_GET['type'] === 'spot') {
                $row = self::get_report_row($_GET['id'], 'spot-pulse');
            } else {
                $row = self::get_report_row($_GET['id'], 'pulse');
            }

            if($row){
                $row = $row[0];
                $date = $row->time;
                $date = date("F j, Y \a\\t g A", strtotime($date));
                $result = json_decode($row->results,true);
                require_once( PAGEZII__SMB_PLUGIN_DIR . 'views/pulse-report.php' );
            } else {
                wp_redirect($next);
            }
        } elseif($_GET['delete'] !== null) {
            $title = 'Pulse Reports';
            $cancelLink = menu_page_url('pzsmb-reports-pulse', false);
            $nonce_field_name = 'delete-pulse-report_'.$_GET['delete'];
            require_once( PAGEZII__SMB_PLUGIN_DIR . 'views/delete-report.php' );
        } else {
            require_once( PAGEZII__SMB_PLUGIN_DIR . 'views/reports-pulse.php' );
        }

        if($_POST['postid']) {
            $id = $_POST['postid'];
            check_admin_referer( 'delete-pulse-report_'.$id );
            global $wpdb;
            $tablename = $wpdb->prefix . 'pagezii_smb_reports';
            $wpdb->delete( $tablename, array( 'ID' => $id ) );
            wp_redirect($next);
        }
    }

    public static function reports_author_menu() {
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        $next = menu_page_url( 'pzsmb-reports-authors', false );

        if($_GET['id'] !== null){
            if($_GET['id'] === ''){
                wp_redirect($next);
            }
            self::load_author_report_resources();

            if($_GET['type'] === 'spot') {
                $row = self::get_report_row($_GET['id'],'spot-author');
            } else {
                $row = self::get_report_row($_GET['id'],'author');
            }

            if($row){
                $row = $row[0];
                $result = json_decode($row->results,true);
                $date = date("F Y", strtotime($result['dates'][1]['date']));
                $imagesUrl = plugin_dir_url( __FILE__ ). 'images/';
                require_once( PAGEZII__SMB_PLUGIN_DIR . 'views/author-report.php' );
            } else {
                wp_redirect($next);
            }
        } elseif($_GET['delete'] !== null) {
            $title = 'Author Reports';
            $cancelLink = menu_page_url('pzsmb-reports-authors', false);
            $nonce_field_name = 'delete-author-report_'.$_GET['delete'];
            require_once( PAGEZII__SMB_PLUGIN_DIR . 'views/delete-report.php' );
        } else {
            require_once( PAGEZII__SMB_PLUGIN_DIR . 'views/reports-author.php' );
        }

        if($_POST['postid']) {
            $id = $_POST['postid'];
            check_admin_referer( 'delete-author-report_'.$id );
            global $wpdb;
            $tablename = $wpdb->prefix . 'pagezii_smb_reports';
            $wpdb->delete( $tablename, array( 'ID' => $id ) );
            wp_redirect($next);
        }

    }

    public static function update_pz_settings($page){
        $pz_settings = get_option('pagezii_smb_settings', []);

        if($page == 'main') {
            $pz_settings['to_email'] = (string) sanitize_email($_POST['to_email']);
            $pz_settings['report_day'] = (string) sanitize_text_field($_POST['report_day']);
            $pz_settings['pulse_reports'] = isset($_POST['pulse_reports']) ? (bool) absint($_POST['pulse_reports']) : false;
            $pz_settings['author_reports'] = isset($_POST['author_reports']) ? (bool) absint($_POST['author_reports']) : false;
        }
        update_option('pagezii_smb_settings', $pz_settings);
        return $pz_settings;
    }

    public static function get_pz_settings(){
        $pz_settings = get_option('pagezii_smb_settings');
        $user = wp_get_current_user();
        $to_email = $user->user_email;
        $reportDay = date("N");

        if(!$pz_settings){
            $pz_settings = array(
                'to_email' => (string) sanitize_email($to_email),
                'report_day' => (string) $reportDay,
                'pulse_reports' => true,
                'author_reports' => true,
            );
        }
        return $pz_settings;
    }

}
