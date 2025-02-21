<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register the admin menu page.
 */
function slu_admin_menu() {
    add_menu_page(
        'Legitimate Users',           // Page title.
        'Legitimate Users',           // Menu title.
        'read',                       // Capability – we'll handle access inside the callback.
        'slu_legitimate_users',       // Menu slug.
        'slu_render_admin_page',      // Callback function.
        'dashicons-admin-users',      // Icon.
        6                             // Position.
    );
}
add_action('admin_menu', 'slu_admin_menu');

function slu_admin_enqueue_scripts( $hook ) {
    if ( $hook !== 'toplevel_page_slu_legitimate_users' ) {
        return;
    }
    wp_enqueue_style('bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css', array(), '4.5.2');
    wp_enqueue_script('bootstrap-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery'), '4.5.2', true);
}
add_action( 'admin_enqueue_scripts', 'slu_admin_enqueue_scripts' );


/**
 * Render the admin page for managing legitimate users.
 */
function slu_render_admin_page() {
    // Only allow administrators and shop managers.
    $current_user = wp_get_current_user();
    if ( ! in_array( 'administrator', $current_user->roles ) && ! in_array( 'shop_manager', $current_user->roles ) ) {
        echo '<div class="notice notice-error"><p>You do not have sufficient permissions to access this page.</p></div>';
        return;
    }
    
    // Handle delete action if triggered via GET.
    if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete' && isset( $_GET['id'] ) ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'legitimate_users';
        $id = intval( $_GET['id'] );
        $deleted = $wpdb->delete( $table_name, array( 'id' => $id ), array( '%d' ) );
        if ( $deleted ) {
            echo '<div class="notice notice-success is-dismissible"><p>Record deleted successfully.</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>Failed to delete record.</p></div>';
        }
    }
    
    // Get filter parameter for status.
    $status_filter = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : '';
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'legitimate_users';
    
    // Build query based on filter if set.
    $query = "SELECT * FROM $table_name";
    if ( $status_filter ) {
        $query .= $wpdb->prepare( " WHERE status = %s", $status_filter );
    }
    $results = $wpdb->get_results( $query );
    ?>
    <div class="wrap">
        <h1>Legitimate Users</h1>
        <!-- Filter Form -->
        <form method="get" class="form-inline">
            <input type="hidden" name="page" value="slu_legitimate_users" />
            <label for="status" class="mr-2">Filter by Status:</label>
            <select name="status" id="status" class="form-control mr-2">
                <option value="">All</option>
                <option value="pending" <?php selected( $status_filter, 'pending' ); ?>>Pending</option>
                <option value="accept" <?php selected( $status_filter, 'accept' ); ?>>Accept</option>
                <option value="reject" <?php selected( $status_filter, 'reject' ); ?>>Reject</option>
            </select>
            <input type="submit" class="btn btn-primary" value="Filter" />
        </form>
        <br/>
        <!-- Table -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>User Name</th>
                    <th>NIC</th>
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
                <?php if ( $results ) : ?>
                    <?php foreach ( $results as $row ) : 
                        // Get user name from user_id.
                        $user_info = get_userdata( $row->user_id );
                        $user_name = $user_info ? $user_info->display_name : 'N/A';
                    ?>
                    <tr>
                        <td><?php echo esc_html( $row->id ); ?></td>
                        <td><?php echo esc_html( $row->user_id ); ?></td>
                        <td><?php echo esc_html( $user_name ); ?></td>
                        <td><?php echo esc_html( $row->nic ); ?></td>
                        <td><?php echo esc_html( $row->country ); ?></td>
                        <td>
                            <?php if ( $row->identity_verification ) : ?>
                                <a class="btn btn-sm btn-info" target="_blank" href="<?php echo esc_url( $row->identity_verification ); ?>">View</a>
                            <?php else : echo 'N/A'; endif; ?>
                        </td>
                        <td>
                            <?php if ( $row->financial_qualification ) : ?>
                                <a class="btn btn-sm btn-info" target="_blank" href="<?php echo esc_url( $row->financial_qualification ); ?>">View</a>
                            <?php else : echo 'N/A'; endif; ?>
                        </td>
                        <td>
                            <?php if ( $row->bca ) : ?>
                                <a class="btn btn-sm btn-info" target="_blank" href="<?php echo esc_url( $row->bca ); ?>">View</a>
                            <?php else : echo 'N/A'; endif; ?>
                        </td>
                        <td>
                            <?php if ( $row->pdf_location ) : ?>
                                <a class="btn btn-sm btn-info" target="_blank" href="<?php echo esc_url( $row->pdf_location ); ?>">View</a>
                            <?php else : echo 'N/A'; endif; ?>
                        </td>
                        <td><?php echo esc_html( $row->status ); ?></td>
                        <td><?php echo esc_html( $row->submitted_at ); ?></td>
                        <td>
                            <a href="<?php echo admin_url( 'admin.php?page=slu_legitimate_users&action=edit&id=' . $row->id ); ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="<?php echo admin_url( 'admin.php?page=slu_legitimate_users&action=delete&id=' . $row->id ); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr><td colspan="12">No records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php

    // Process the update if the form is submitted.
    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['slu_action'] ) && $_POST['slu_action'] === 'update' ) {
        if ( ! isset( $_POST['slu_edit_nonce'] ) || ! wp_verify_nonce( $_POST['slu_edit_nonce'], 'slu_edit_entry' ) ) {
            echo '<div class="notice notice-error"><p>Nonce verification failed.</p></div>';
        } else {
            global $wpdb;
            $table_name = $wpdb->prefix . 'legitimate_users';
            $id = intval( $_POST['id'] );
            $data = array(
                'name'    => sanitize_text_field( $_POST['name'] ),
                'nic'     => sanitize_text_field( $_POST['nic'] ),
                'country' => sanitize_text_field( $_POST['country'] ),
                'status'  => sanitize_text_field( $_POST['status'] )
            );
            $format = array( '%s', '%s', '%s', '%s' );
            
            // Set up uploads directory.
            $upload_dir = wp_upload_dir();
            $uploads_base = $upload_dir['basedir'] . '/slu_uploads';
            if ( ! file_exists( $uploads_base ) ) {
                wp_mkdir_p( $uploads_base );
            }
            
            // Handle file updates for each file field, if a new file is uploaded.
            $file_fields = array( 'identity_verification', 'financial_qualification', 'bca' );
            foreach ( $file_fields as $field ) {
                if ( isset( $_FILES[$field] ) && ! empty( $_FILES[$field]['name'] ) ) {
                    $filename = basename( $_FILES[$field]['name'] );
                    $target_file = $uploads_base . '/' . time() . '_' . $filename;
                    if ( move_uploaded_file( $_FILES[$field]['tmp_name'], $target_file ) ) {
                        $file_url = str_replace( ABSPATH, site_url( '/' ), $target_file );
                        $data[$field] = $file_url;
                        // Ensure format is updated (string).
                        $format[$field] = '%s';
                    }
                }
            }
            
            $updated = $wpdb->update( $table_name, $data, array( 'id' => $id ), $format, array( '%d' ) );
            if ( $updated !== false ) {
                echo '<div class="notice notice-success is-dismissible"><p>Record updated successfully.</p></div>';
            } else {
                echo '<div class="notice notice-error is-dismissible"><p>Failed to update record.</p></div>';
            }
        }
    }

    // Check if the URL indicates an edit action.
    if ( isset( $_GET['action'] ) && $_GET['action'] === 'edit' && isset( $_GET['id'] ) ) {
        global $wpdb;
        $id = intval( $_GET['id'] );
        $table_name = $wpdb->prefix . 'legitimate_users';
        $record = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id ) );
        
        if ( ! $record ) {
            echo '<div class="notice notice-error"><p>Record not found.</p></div>';
        } else {
            // Display the edit form.
            ?>
            <div class="wrap">
                <h1>Edit Legitimate User</h1>
                <form method="post" enctype="multipart/form-data">
                    <?php wp_nonce_field( 'slu_edit_entry', 'slu_edit_nonce' ); ?>
                    <input type="hidden" name="slu_action" value="update">
                    <input type="hidden" name="id" value="<?php echo esc_attr( $record->id ); ?>">
                    
                    <div class="form-group">
                        <label for="name">Government Registered Name</label>
                        <input type="text" name="name" class="form-control" id="name" value="<?php echo esc_attr( $record->name ); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="nic">NIC Number</label>
                        <input type="text" name="nic" class="form-control" id="nic" value="<?php echo esc_attr( $record->nic ); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="country">Country</label>
                        <input type="text" name="country" class="form-control" id="country" value="<?php echo esc_attr( $record->country ); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="pending" <?php selected( $record->status, 'pending' ); ?>>Pending</option>
                            <option value="accept" <?php selected( $record->status, 'accept' ); ?>>Accept</option>
                            <option value="reject" <?php selected( $record->status, 'reject' ); ?>>Reject</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Identity Verification</label>
                        <?php if ( $record->identity_verification ) : ?>
                            <p>Current: <a href="<?php echo esc_url( $record->identity_verification ); ?>" target="_blank">View</a></p>
                        <?php endif; ?>
                        <input type="file" name="identity_verification" class="form-control-file">
                    </div>
                    <div class="form-group">
                        <label>Financial Qualification</label>
                        <?php if ( $record->financial_qualification ) : ?>
                            <p>Current: <a href="<?php echo esc_url( $record->financial_qualification ); ?>" target="_blank">View</a></p>
                        <?php endif; ?>
                        <input type="file" name="financial_qualification" class="form-control-file">
                    </div>
                    <div class="form-group">
                        <label>BCA</label>
                        <?php if ( $record->bca ) : ?>
                            <p>Current: <a href="<?php echo esc_url( $record->bca ); ?>" target="_blank">View</a></p>
                        <?php endif; ?>
                        <input type="file" name="bca" class="form-control-file">
                    </div>
                    
                    <input type="submit" class="btn btn-primary" value="Update">
                    <a href="<?php echo admin_url( 'admin.php?page=slu_legitimate_users' ); ?>" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
            <?php
        }
    }

}

