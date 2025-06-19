<div class="legitimate-user-form">
    <!-- <h2>Legitimate User</h2> -->
    <form id="slu-form" method="post" enctype="multipart/form-data">
        <?php wp_nonce_field('slu_form_submit','slu_nonce'); ?>
        <input type="hidden" name="action" value="slu_submit_form">
        <div class="form-group">
            <label for="slu-name">Government Registered Name</label>
            <input type="text" class="form-control" id="slu-name" name="name" required>
        </div>
        <!-- <div class="form-group">
            <label for="slu-nic">NIC Number</label>
            <input type="text" class="form-control" id="slu-nic" name="nic">
        </div> -->
        <div class="form-group">
            <label for="slu-country">Country</label>
            <input type="text" class="form-control" id="slu-country" name="country" required>
        </div>
        <div class="form-group">
            <label for="slu-identity">Identity Verification (Passport/ID copy)</label>
            <input type="file" class="form-control-file" id="slu-identity" name="identity_verification" accept="image/*" required>
        </div>
        <!-- <div class="form-group">
            <label for="slu-financial">Financial Qualification (Proof of Funds)</label>
            <input type="file" class="form-control-file" id="slu-financial" name="financial_qualification" accept="image/*" required>
        </div> -->
        <!-- <div class="form-group">
            <label for="slu-bca">Buyers Confidentiality Agreement Signature (BCA)</label>
            <input type="file" class="form-control-file" id="slu-bca" name="bca" accept="image/*">
        </div> -->
        <button type="submit" class="btn btn-primary" style="width: 100%; background-color: #1e1e19; border: none; outline: none; box-shadow: none;">Submit</button>
    </form>
</div>