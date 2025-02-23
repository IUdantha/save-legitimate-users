<div class="modal fade" id="sluModal" tabindex="-1" role="dialog" aria-labelledby="sluModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="sluModalLabel">Legitimate User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="slu-form" method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('slu_form_submit','slu_nonce'); ?>
            <input type="hidden" name="action" value="slu_submit_form">
            <div class="form-group">
                <label for="slu-name">Government Registered Name</label>
                <input type="text" class="form-control" id="slu-name" name="name" required>
            </div>
            <div class="form-group">
                <label for="slu-nic">NIC Number</label>
                <input type="text" class="form-control" id="slu-nic" name="nic" required>
            </div>
            <div class="form-group">
                <label for="slu-country">Country</label>
                <input type="text" class="form-control" id="slu-country" name="country" required>
            </div>
            <div class="form-group">
                <label for="slu-identity">Identity Verification (Passport/ID copy)</label>
                <input type="file" class="form-control-file" id="slu-identity" name="identity_verification" accept="image/*" required>
            </div>
            <div class="form-group">
                <label for="slu-financial">Financial Qualification (Proof of Funds)</label>
                <input type="file" class="form-control-file" id="slu-financial" name="financial_qualification" accept="image/*" required>
            </div>
            <div class="form-group">
                <label for="slu-bca">Buyers Confidentiality Agreement Signature (BCA)</label>
                <input type="file" class="form-control-file" id="slu-bca" name="bca" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
      </div>
    </div>
  </div>
</div>
