<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pagezii_SMB_Main {

    public static function get_current_page_url(){
        global $wp;
        return add_query_arg( $_SERVER['QUERY_STRING'], '', home_url( $wp->request ) );
    }

    public static function get_user_ip(){
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public static function buffering(){
        ob_start();
    }

    public static function plugin_activation(){
        require_once( PAGEZII__SMB_PLUGIN_DIR . 'class.pagezii-admin.php' );
        $settings = Pagezii_SMB_Admin::get_pz_settings();

        add_option('pagezii_smb_settings', $settings);
        add_option('pagezii_smb_db_version', PAGEZII_SMB_VERSION);
        self::plugin_db_install();
        self::plugin_db_install_data();

        if ( ! wp_next_scheduled( 'pagezii_smb_pulse_job' ) ) {
            wp_schedule_event( time(), 'weekly', 'pagezii_smb_pulse_job' );
        }

        if ( ! wp_next_scheduled( 'pagezii_smb_author_job' ) ) {
            wp_schedule_event( time(), 'monthly', 'pagezii_smb_author_job' );
        }
    }

    public static function plugin_db_install(){
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $table_name = $wpdb->prefix . 'pagezii_smb_dashboard';
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            field1 tinytext DEFAULT '' NOT NULL,
            field2 text DEFAULT '' NOT NULL,
            field3 varchar(200) DEFAULT '' NOT NULL,
            field4 varchar(200) DEFAULT '' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta( $sql );

        $table_name = $wpdb->prefix . 'pagezii_smb_reports';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            type varchar(55) DEFAULT '' NOT NULL,
            results text DEFAULT '' NOT NULL,
            results2 text DEFAULT '' NOT NULL,
            info varchar(300) DEFAULT '' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta( $sql );

    }

    public static function plugin_db_install_data(){
        global $wpdb;

        $table_name = $wpdb->prefix . 'pagezii_smb_dashboard';

        $wpdb->insert(
            $table_name,
            array(
                'time' => current_time( 'mysql' ),
                'field1' => 'Data',
            )
        );
    }

    public static function plugin_deactivation(){
        delete_option('pagezii_smb_api_key');
        delete_option('pagezii_smb_settings');
        delete_option('pagezii_smb_db_version');
        if ( wp_next_scheduled( 'pagezii_smb_pulse_job' ) ) {
            wp_clear_scheduled_hook( 'pagezii_smb_pulse_job' );
        }
        if ( wp_next_scheduled( 'pagezii_smb_author_job' ) ) {
            wp_clear_scheduled_hook( 'pagezii_smb_author_job' );
        }
    }

    public static function plugin_uninstall()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'pagezii_smb_dashboard';
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query($sql);

        $table_name = $wpdb->prefix . 'pagezii_smb_reports';
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query($sql);
    }

    public static function http_get($request, $path=null){
        $http_args = array(
            'body' => $request,
            'headers' => array(),
            'httpversion' => '1.0',
            'timeout' => 25
        );
        $url = PAGEZII__SMB_PLUGIN_API;
        $response = wp_remote_get( $url, $http_args );
        return $response;
    }

    public static function build_query($args){
        return _http_build_query($args, '', '&');
    }

    public static function send_email($email, $subject, $message, $report_link){
        $subject = (string) stripslashes(sanitize_text_field($subject));
        $from = (string) stripslashes(sanitize_text_field($email));
        $from_name = 'Pagezii Blog SEO Plugin';
        $blogUrl = get_site_url();

        ob_start();
        include( PAGEZII__SMB_PLUGIN_DIR . 'views/email-reports.php' );
        $body = ob_get_clean();
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'From: '.$from_name.' <'.$from.'>';
        $response = wp_mail( $email, $subject, $body, $headers, array() );
    }

    public static function custom_intervals($schedules)
    {
        // add a 'weekly' interval
        $schedules['weekly'] = array(
            'interval' => 604800,
            'display' => __('Once Weekly')
        );
        $schedules['monthly'] = array(
            'interval' => 2635200,
            'display' => __('Once a month')
        );
        return $schedules;
    }

    public static function pulse_cron_job(){
        $pz_settings = get_option('pagezii_smb_settings');
        $email = $pz_settings['to_email'];
        if(!$email){
            return;
        }

        $data = [];
        $posts = get_posts(['numberposts' => 20]);
        foreach($posts as $row){
            $first_name = get_user_meta($row->post_author,'first_name',true);
            $last_name = get_user_meta($row->post_author,'last_name',true);
            $author = $first_name ? "$first_name $last_name": $row->post_author;
            $permalink = get_post_permalink($row->ID);
            $data[] = [
                'id' => $row->ID,
                'date' => $row->post_date,
                'author' => $author,
                'comments' => $row->comment_count,
            ];
        }

        require_once( PAGEZII__SMB_PLUGIN_DIR . 'class.pagezii-admin.php' );

        $request = array(
            'val' => $data,
            'type' => 'pulse',
            'blog' => get_site_url(),
            'dates' => '',
        );

        $response = self::http_get( $request );
        if(is_array($response) && isset($response['body'])){
            $response = json_decode($response['body'], true);
            if(is_array($response)){
                $response = Pagezii_SMB_Admin::savePulseData($response,'pulse');
                $link = $response['link'];
            } else {
                wp_die("Error with job");
            }
        } else {
            wp_die("Error with job");
        }
        $message = 'Your report is now ready to view.';
        $subject = 'Pagezii Blog Pulse Report';
        $mail_response = self::send_email($email, $subject, $message, $link);
    }

    public static function author_cron_job(){

        $pz_settings = get_option('pagezii_smb_settings');
        $email = $pz_settings['to_email'];
        if(!$email){
            return;
        }

        $data = [];
        $posts = get_posts(['numberposts' => 120]);
        foreach($posts as $row){
            $first_name = get_user_meta($row->post_author,'first_name',true);
            $last_name = get_user_meta($row->post_author,'last_name',true);
            $author = $first_name ? "$first_name $last_name": $row->post_author;
            $permalink = get_post_permalink($row->ID);
            $data[] = [
                'id' => $row->ID,
                'date' => $row->post_date,
                'author' => $author,
                'comments' => $row->comment_count,
            ];
        }

        require_once( PAGEZII__SMB_PLUGIN_DIR . 'class.pagezii-admin.php' );

        $startDate = strtotime("First day of last month");

        $request = array(
            'val' => $data,
            'type' => 'author',
            'blog' => get_site_url(),
            'dates' => date("Y-m-d", $startDate),
        );

        $response = self::http_get( $request );

        if(is_array($response) && isset($response['body'])){
            $response = json_decode($response['body'], true);
            if(is_array($response)){
                $response = Pagezii_SMB_Admin::saveAuthorData($response,'author');
                $link = $response['link'];
            } else {
                wp_die("Error with job");
            }
        } else {
            wp_die("Error with job");
        }

        $month = date("F", $startDate);
        $message = 'Your author report for the month of ' . $month . ' is ready to view.';
        $subject = 'Pagezii Blog Author Report';
        $mail_response = self::send_email($email, $subject, $message, $link);

    }

}
