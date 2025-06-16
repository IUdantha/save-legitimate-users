<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>User Status Changed</title>
  <style>
    body { font-family: Arial, sans-serif; line-height:1.6; color: #333; }
    .container { max-width: 600px; margin: auto; padding: 20px; }
    .header { background: #333; color: #fff; padding: 10px; border-radius: 4px; text-align: center; }
    .content { margin-top: 20px; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h2>User Status Updated</h2>
    </div>
    <div class="content">
      <p>Hello Admin,</p>
      <p>The verification status for user <strong><?php echo esc_html( $user_name ); ?> (ID: <?php echo intval( $user_id ); ?>)</strong> has been changed to <strong><?php echo strtoupper( $status ); ?></strong>.</p>

      <ul>
        <li><strong>Paddle Number:</strong> <?php echo intval( $paddle_number ); ?></li>
        <li><strong>Changed At:</strong> <?php echo esc_html( current_time('mysql') ); ?></li>
      </ul>

      <p>Regards,<br/>agam.art</p>
    </div>
  </div>
</body>
</html>
