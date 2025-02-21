<?php
if (!defined('ABSPATH')) exit;
/**
 * Handle the AJAX form submission.
 */
function slu_handle_form_submission(){
    // Verify nonce for security
    if(!isset($_POST['slu_nonce']) || !wp_verify_nonce($_POST['slu_nonce'], 'slu_form_submit')){
        wp_send_json_error(array('message' => 'Security check failed.'));
        exit;
    }

    // Ensure the user is logged in
    if(!is_user_logged_in()){
        wp_send_json_error(array('message' => 'User not logged in.'));
        exit;
    }

    $user_id = get_current_user_id();
    $name    = sanitize_text_field($_POST['name']);
    $nic     = sanitize_text_field($_POST['nic']);
    $country = sanitize_text_field($_POST['country']);

    // Set up uploads directory
    $upload_dir = wp_upload_dir();
    $uploads_base = $upload_dir['basedir'] . '/slu_uploads';
    if( ! file_exists($uploads_base) ){
        wp_mkdir_p($uploads_base);
    }

    // Process file uploads
    $uploaded_files = array();
    $fields = array('identity_verification', 'financial_qualification', 'bca');
    foreach ($fields as $field){
        if(isset($_FILES[$field]) && !empty($_FILES[$field]['name'])){
            $filename = basename($_FILES[$field]['name']);
            $target_file = $uploads_base . '/' . time() . '_' . $filename;
            if(move_uploaded_file($_FILES[$field]['tmp_name'], $target_file)){
                // Convert file path to URL
                $uploaded_files[$field] = str_replace(ABSPATH, site_url('/') , $target_file);
            } else {
                $uploaded_files[$field] = '';
            }
        } else {
            $uploaded_files[$field] = '';
        }
    }

    // Generate PDF using FPDF
    require_once SLU_PLUGIN_DIR . 'includes/fpdf/fpdf.php';
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(40,10,'Legitimate User Form Submission');
    $pdf->Ln(20);
    $pdf->SetFont('Arial','',12);
    $pdf->Cell(50,10,'Government Registered Name: ' . $name);
    $pdf->Ln(10);
    $pdf->Cell(50,10,'NIC Number: ' . $nic);
    $pdf->Ln(10);
    $pdf->Cell(50,10,'Country: ' . $country);
    $pdf->Ln(10);
    // You can add additional fields if needed

    // Save the PDF
    $pdf_dir = $uploads_base . '/slu_uploads/pdfs';
    if( ! file_exists($pdf_dir) ){
        wp_mkdir_p($pdf_dir);
    }
    $pdf_filename = $pdf_dir . '/submission_' . $user_id . '_' . time() . '.pdf';
    $pdf->Output('F', $pdf_filename);
    $pdf_url = str_replace(ABSPATH, site_url('/') , $pdf_filename);

    // Insert submission data into the database
    global $wpdb;
    $table_name = $wpdb->prefix . 'legitimate_users';
    $result = $wpdb->insert($table_name, array(
        'user_id'                => $user_id,
        'name'                   => $name,
        'nic'                    => $nic,
        'country'                => $country,
        'identity_verification'  => $uploaded_files['identity_verification'],
        'financial_qualification'=> $uploaded_files['financial_qualification'],
        'bca'                    => $uploaded_files['bca'],
        'pdf_location'           => $pdf_url,
        'status'                 => 'pending'
    ), array('%d','%s','%s','%s','%s','%s','%s','%s','%s'));

    if($result){
        wp_send_json_success(array('message' => 'Form submitted successfully'));
    } else {
        wp_send_json_error(array('message' => 'Database error occurred'));
    }
    exit;
}
add_action('wp_ajax_slu_submit_form', 'slu_handle_form_submission');
// Though the form is for logged-in users, you can include this if needed
add_action('wp_ajax_nopriv_slu_submit_form', 'slu_handle_form_submission');
