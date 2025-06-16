<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Status Update</title>
  <style>
    body { font-family: Arial, sans-serif; line-height:1.6; color: #333; }
    .container { max-width: 600px; margin: auto; padding: 20px; }
    .header { background: #1e1e19; color: #fff; padding: 10px; border-radius: 4px; text-align: center; }
    .content { margin-top: 20px; }
    .btn { display: inline-block; padding: 10px 20px; background: #1e1e19; color: #fff; text-decoration: none; border-radius: 4px; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h2>Your Verification Status Has Changed</h2>
    </div>
    <div class="content">
      <p>Hi <?php echo esc_html( $user_name ); ?>,</p>

      <?php if ( 'accept' === $status ) : ?>
        <p>🎉 <strong>Congratulations!</strong> You are now a Qualified Collector.</p>
        <p><a href="<?php echo esc_url( $bid_link ); ?>" class="btn" style="color:#fff;">Start Your Bid Now</a></p>
      <?php elseif ( 'pending' === $status ) : ?>
        <p>⏳ <strong>Sorry, you are still in the pending state.</strong></p>
        <p>We’ll notify you again once your status changes.</p>
      <?php else : // reject ?>
        <p>❌ <strong>Sorry, your request was declined.</strong></p>
        <p>If you’d like to try again, please contact support.</p>
      <?php endif; ?>

      <p>– The Team</p>
    </div>
  </div>
</body>
</html>
