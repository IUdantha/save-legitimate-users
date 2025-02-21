<?php
if (!defined('ABSPATH')) exit;

/**
 * Create the legitimate_users table on plugin activation.
 */
function slu_create_table(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'legitimate_users';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        name varchar(255) NOT NULL,
        nic varchar(50) NOT NULL,
        country varchar(100) NOT NULL,
        identity_verification varchar(255) NOT NULL,
        financial_qualification varchar(255) NOT NULL,
        bca varchar(255) NOT NULL,
        pdf_location varchar(255) NOT NULL,
        status varchar(20) NOT NULL DEFAULT 'pending',
        submitted_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/**
 * Enqueue the JavaScript file that shows the popup form.
 */
function slu_enqueue_scripts(){
    if ( is_user_logged_in() ) {
        wp_enqueue_script('slu-popup', SLU_PLUGIN_URL . 'assets/js/popup.js', array('jquery'), '1.0', true );
        wp_localize_script('slu-popup', 'slu_ajax_object', array( 'ajax_url' => admin_url('admin-ajax.php') ) );
    }
}

/**
 * Check if the current user has already submitted the form.
 *
 * @param int $user_id
 * @return bool
 */
function slu_has_submitted_form($user_id){
    global $wpdb;
    $table_name = $wpdb->prefix . 'legitimate_users';
    $result = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE user_id = %d", $user_id));
    return $result > 0;
}

/**
 * Render the popup form template.
 */
function slu_render_form(){
    include SLU_PLUGIN_DIR . 'templates/form-template.php';
}

/**
 * Hook into the footer to output the form if the user hasn’t submitted it.
 */
function slu_show_popup_form(){
    if ( is_user_logged_in() ) {
        $user_id = get_current_user_id();
        if( ! slu_has_submitted_form($user_id) ){
            // You can wrap this output in a container for your modal
            slu_render_form();
        }
    }
}
