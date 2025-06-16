<?php
/**
 * slu-gatekeeper.php
 * Gatekeeps access to wp-content/uploads/slu_uploads/
 */

// 1) Bootstrap WP
require_once dirname(__FILE__, 4) . '/wp-load.php';

// 2) Define our protected base directory
$base_dir = WP_CONTENT_DIR . '/uploads/slu_uploads/';

// 3) Grab & sanitize the requested file path
$file = isset($_GET['file']) ? wp_unslash($_GET['file']) : '';
$file_path = realpath( $base_dir . $file );

// 4) Prevent directory traversal
if ( ! $file_path || strpos( $file_path, realpath($base_dir) ) !== 0 ) {
    status_header(403);
    exit('Forbidden');
}

// 5) Only allow logged-in administrators
if ( ! is_user_logged_in() || ! current_user_can( 'administrator' ) ) {
    status_header(403);
    exit('Forbidden');
}

// 6) Stream the file if it exists
if ( file_exists( $file_path ) ) {
    $mime = function_exists('mime_content_type')
      ? mime_content_type( $file_path )
      : wp_check_filetype( $file_path )['type'];
    header( 'Content-Type: ' . $mime );
    header( 'Content-Length: ' . filesize( $file_path ) );
    readfile( $file_path );
    exit;
}

// 7) If we reach here, file not found
status_header(404);
exit('Not found');
