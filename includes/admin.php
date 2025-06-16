<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add admin menu page.
 * We’re using a capability that both administrators and shop managers have.
 */
function slu_admin_menu() {
    add_menu_page(
        'Legitimate Users',
        'Legitimate Users',
        'edit_posts', // shop_manager and admin can edit posts by default
        'slu-legitimate-users',
        'slu_admin_page_callback',
        'dashicons-groups', // icon
        6
    );
}

/**
 * Enqueue Bootstrap and our custom admin JS for this page.
 */
function slu_admin_enqueue_scripts($hook) {
    if ( $hook !== 'toplevel_page_slu-legitimate-users' ) {
        return;
    }
    // Enqueue Bootstrap CSS & JS (from a CDN)
    wp_enqueue_style('bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css', array(), '4.5.2');
    wp_enqueue_script('bootstrap-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery'), '4.5.2', true);

    // Enqueue our custom admin JS for handling edit/delete actions
    wp_enqueue_script('slu-admin-js', SLU_PLUGIN_URL . 'assets/js/slu-admin.js', array('jquery', 'bootstrap-js'), '1.0', true);
    wp_localize_script('slu-admin-js', 'slu_admin_ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}

/**
 * Callback function to render the admin page.
 */
function slu_admin_page_callback() {
    // Check permissions – we already require 'edit_posts'
    if( ! current_user_can('edit_posts') ) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'legitimate_users';

    // Optional: filter by status if set in the URL (e.g., ?page=slu-legitimate-users&status=pending)
    $status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';

    $query = "SELECT * FROM $table_name";
    if( $status_filter ) {
        $query .= $wpdb->prepare(" WHERE status = %s", $status_filter);
    }
    $query .= " ORDER BY submitted_at DESC";
    $results = $wpdb->get_results($query);
    ?>

    <div class="wrap">
        <h1>Legitimate Users</h1>

        <!-- Status Filter -->
        <form method="get" class="form-inline mb-3">
            <input type="hidden" name="page" value="slu-legitimate-users">
            <div class="form-group mr-2">
                <label for="status">Filter by Status: </label>
                <select name="status" id="status" class="form-control ml-2">
                    <option value="">All</option>
                    <option value="pending" <?php selected($status_filter, 'pending'); ?>>Pending</option>
                    <option value="accept" <?php selected($status_filter, 'accept'); ?>>Accept</option>
                    <option value="reject" <?php selected($status_filter, 'reject'); ?>>Reject</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <!-- Table of entries -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>User Name</th>
                    <th>Paddle Number</th>
                    <!-- <th>NIC</th> -->
                    <th>Country</th>
                    <th>Identity Verification</th>
                    <th>Financial Qualification</th>
                    <th>BCA</th>
                    <th>Generated Doc</th>
                    <th>Status</th>
                    <th>Submitted Date Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if( $results ) {
                foreach( $results as $row ) {
                    // Retrieve the user’s display name based on the user_id
                    $user_info = get_userdata($row->user_id);
                    $user_name = ( $user_info ) ? $user_info->display_name : 'Unknown';
                    echo '<tr>';
                    echo '<td>' . esc_html($row->id) . '</td>';
                    echo '<td>' . esc_html($row->user_id) . '</td>';
                    echo '<td>' . esc_html($user_name) . '</td>';
                    echo '<td>' . esc_html($row->paddle_number) . '</td>';
                    // echo '<td>' . esc_html($row->nic) . '</td>';
                    echo '<td>' . esc_html($row->country) . '</td>';
                    // Each link opens the file in a new tab
                    echo '<td><a class="btn btn-sm btn-info" href="' . esc_url($row->identity_verification) . '" target="_blank">View</a></td>';
                    echo '<td><a class="btn btn-sm btn-info" href="' . esc_url($row->financial_qualification) . '" target="_blank">View</a></td>';
                    echo '<td><a class="btn btn-sm btn-info" href="' . esc_url($row->bca) . '" target="_blank">View</a></td>';
                    echo '<td><a class="btn btn-sm btn-info" href="' . esc_url($row->pdf_location) . '" target="_blank">View</a></td>';
                    echo '<td>' . esc_html($row->status) . '</td>';
                    echo '<td>' . esc_html($row->submitted_at) . '</td>';
                    echo '<td>';
                    echo '<button class="btn btn-sm btn-warning edit-entry" data-id="' . esc_attr($row->id) . '">Edit</button> ';
                    echo '<button class="btn btn-sm btn-danger delete-entry" data-id="' . esc_attr($row->id) . '">Delete</button>';
                    echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="12">No records found.</td></tr>';
            }
            ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap Modal for Editing an Entry -->
    <div class="modal fade" id="editEntryModal" tabindex="-1" role="dialog" aria-labelledby="editEntryModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <form id="editEntryForm">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editEntryModalLabel">Edit Entry</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="entry_id" id="entry_id">
                <?php wp_nonce_field('slu_edit_entry','slu_edit_nonce'); ?>
                <div class="form-group">
                    <label for="edit-name">Government Registered Name</label>
                    <input type="text" class="form-control" id="edit-name" name="name" required>
                </div>
                <!-- <div class="form-group">
                    <label for="edit-nic">NIC Number</label>
                    <input type="text" class="form-control" id="edit-nic" name="nic">
                </div> -->
                <div class="form-group">
                    <label for="edit-country">Country</label>
                    <input type="text" class="form-control" id="edit-country" name="country" required>
                </div>
                <div class="form-group">
                    <label for="edit-status">Status</label>
                    <select class="form-control" id="edit-status" name="status" required>
                        <option value="pending">Pending</option>
                        <option value="accept">Accept</option>
                        <option value="reject">Reject</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Save changes</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <?php
}


/**
 * Handle the edit entry AJAX request.
 */
function slu_handle_edit_entry() {
    if ( ! isset($_POST['slu_edit_nonce']) || ! wp_verify_nonce($_POST['slu_edit_nonce'], 'slu_edit_entry') ) {
        wp_send_json_error(array('message' => 'Nonce verification failed.'));
    }

    $entry_id = intval($_POST['entry_id']);
    $name     = sanitize_text_field($_POST['name']);
    $nic      = sanitize_text_field($_POST['nic']);
    $country  = sanitize_text_field($_POST['country']);
    $status   = sanitize_text_field($_POST['status']);

    global $wpdb;
    $table_name = $wpdb->prefix . 'legitimate_users';
    $result = $wpdb->update($table_name, array(
        'name'    => $name,
        'nic'     => $nic,
        'country' => $country,
        'status'  => $status
    ), array('id' => $entry_id), array('%s','%s','%s','%s'), array('%d'));

    if ( $result !== false ) {
        wp_send_json_success(array('message' => 'Entry updated successfully.'));
    } else {
        wp_send_json_error(array('message' => 'Error updating entry.'));
    }
}
add_action('wp_ajax_slu_edit_entry', 'slu_handle_edit_entry');

/**
 * Handle the delete entry AJAX request.
 */
function slu_handle_delete_entry() {
    $entry_id = intval($_POST['entry_id']);
    global $wpdb;
    $table_name = $wpdb->prefix . 'legitimate_users';
    $result = $wpdb->delete($table_name, array('id' => $entry_id), array('%d'));
    if ( $result ) {
        wp_send_json_success(array('message' => 'Entry deleted successfully.'));
    } else {
        wp_send_json_error(array('message' => 'Error deleting entry.'));
    }
}
add_action('wp_ajax_slu_delete_entry', 'slu_handle_delete_entry');

