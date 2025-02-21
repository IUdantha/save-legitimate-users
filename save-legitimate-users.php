<?php
/*
Plugin Name: Save Legitimate Users
Description: A plugin to collect and save legitimate user details.
Version: 1.1
Author: Isuru Udantha
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Define plugin directory paths
define('SLU_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SLU_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include necessary files
require_once SLU_PLUGIN_DIR . 'includes/functions.php';
require_once SLU_PLUGIN_DIR . 'includes/form-handler.php';

// Load admin files if in the admin area.
if ( is_admin() ) {
    require_once SLU_PLUGIN_DIR . 'includes/admin/admin-page.php';
}

// Activation hook to create the database table
register_activation_hook( __FILE__, 'slu_create_table' );

// Enqueue frontend scripts/styles
add_action('wp_enqueue_scripts', 'slu_enqueue_scripts');

// Display the form popup on the client side (only for logged in users)
add_action('wp_footer', 'slu_show_popup_form');
