<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * @package Pagezii Blog SEO Plugin
 */
/*
Plugin Name: Pagezii Blog SEO Plugin
Plugin URI: https://pagezii.com/wordpress-smb
Description: Pagezii Blog SEO automatically creates detailed blog post metrics, including SEO scores, shares and reader profiles. Monthly author reports provide author metrics on most popular authors of the blog.
Version: 1.0
Author: Pagezii
Author URI: https://pagezii.com
License: GPLv2 or later

Pagezii Blog SEO Plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Pagezii Blog SEO Plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Pagezii SEO Agency Plugin. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define( 'PAGEZII_SMB_VERSION', '1.0' );
define( 'PAGEZII__MINIMUM_WP_VERSION', '3.5' );
define( 'PAGEZII__SMB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PAGEZII__SMB_DEBUG_MODE', false );
if (PAGEZII__SMB_DEBUG_MODE) {
    define( 'PAGEZII__SMB_PLUGIN_API', 'http://localhost:8000/api/wordpress-smb' );
} else {
    define( 'PAGEZII__SMB_PLUGIN_API', 'https://pagezii.com/api/wordpress-smb' );
}

require_once( PAGEZII__SMB_PLUGIN_DIR . 'class.pagezii-main.php' );

add_filter( 'cron_schedules', array( 'Pagezii_SMB_Main', 'custom_intervals' ) );
add_action( 'pagezii_smb_pulse_job', array( 'Pagezii_SMB_Main', 'pulse_cron_job' ), 10, 4 );
add_action( 'pagezii_smb_author_job', array( 'Pagezii_SMB_Main', 'author_cron_job' ), 10, 4 );
add_action( 'init', array( 'Pagezii_SMB_Main', 'buffering' ) );

register_activation_hook( __FILE__, array( 'Pagezii_SMB_Main', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'Pagezii_SMB_Main', 'plugin_deactivation' ) );
register_uninstall_hook( __FILE__, array( 'Pagezii_SMB_Main', 'plugin_uninstall' ) );

if ( is_admin() ) {
    require_once( PAGEZII__SMB_PLUGIN_DIR . 'class.pagezii-admin.php' );
    add_action( 'init', array( 'Pagezii_SMB_Admin', 'init' ) );
}
