<?php
if (!defined('ABSPATH')) exit;

/**
 * Create the legitimate_users table on plugin activation if it doesn't already exist.
 */
function slu_create_table(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'legitimate_users';
    
    // Check if table already exists
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
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
}

/**
 * Enqueue the necessary JavaScript and CSS files, including Bootstrap.
 */
function slu_enqueue_scripts(){
    if ( is_user_logged_in() ) {
        // Enqueue Bootstrap CSS and JS (using a CDN)
        wp_enqueue_style('bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css', array(), '4.5.2');
        wp_enqueue_script('bootstrap-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery'), '4.5.2', true);

        // Enqueue the plugin's custom JS
        wp_enqueue_script('slu-popup', SLU_PLUGIN_URL . 'assets/js/popup.js', array('jquery','bootstrap-js'), '1.0', true );
        wp_localize_script('slu-popup', 'slu_ajax_object', array( 'ajax_url' => admin_url('admin-ajax.php') ) );

        wp_enqueue_style('slu-style', plugin_dir_url(__FILE__) . '../assets/css/style.css');
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
function slu_render_form_popup(){
    include SLU_PLUGIN_DIR . 'templates/form-template-popup.php';
}

/**
 * Render the form template.
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
            // slu_render_form_popup();
        }
    }
}

/**
 * Shortcode callback to display the Legitimate User form.
 */
function slu_form_shortcode($user_id) {
    // Only display the form if the user is logged in
    if ( is_user_logged_in() ) {
        ob_start();
        $user_id = get_current_user_id();
        if( ! slu_has_submitted_form($user_id) ){
            slu_render_form(); // Reuse the form template output function
        } else {
            return '<p>Thanks for the submission, After the administrator review you will grand access to the bidding. Please stay tuned.</p>';
        }
        return ob_get_clean();
    } else {
        return '<p>You need to be logged in to submit the form.</p>';
    }
}

/**
 * Shortcode callback to display the Legitimate User button.
 */
function slu_btn_shortcode($user_id) {
    // Only display the form if the user is logged in
    if ( is_user_logged_in() ) {
        ob_start();
        $user_id = get_current_user_id();
        if( ! slu_has_submitted_form($user_id) ){
            ?><a href="https://agam.art/legitimate/"><button class="slu-header-btn">BECOME A <br />QUALIFIED COLLECTOR</button></a><?php
        } else {
            ?><a href="https://agam.art/legitimate/"><button class="slu-header-btn">VERIFICATION PENDING</button></a><?php
        }
        return ob_get_clean();
    } else {
        return '';
    }
}