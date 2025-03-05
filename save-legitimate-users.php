<?php
/*
Plugin Name: Save Legitimate Users
Description: A plugin to collect and save legitimate user details.
Version: 1.1
Author: Isuru Udantha
Author URI: https://isuruudantha.com/
*/


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Define plugin directory paths
define('SLU_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SLU_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include necessary files
require_once SLU_PLUGIN_DIR . 'includes/functions.php';
require_once SLU_PLUGIN_DIR . 'includes/form-handler.php';

// Include admin side functions
if ( is_admin() ) {
    require_once SLU_PLUGIN_DIR . 'includes/admin.php';
    add_action('admin_enqueue_scripts', 'slu_admin_enqueue_scripts');
    add_action('admin_menu', 'slu_admin_menu');
}

// Activation hook to create the database table
register_activation_hook( __FILE__, 'slu_create_table' );

// Frontend hooks (client side)
add_action('wp_enqueue_scripts', 'slu_enqueue_scripts');
add_action('wp_footer', 'slu_show_popup_form');
add_shortcode('legitimate_user_form', 'slu_form_shortcode');
