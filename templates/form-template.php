<div id="slu-modal" style="display:none; position:fixed; top:20%; left:50%; transform:translateX(-50%); background:#fff; padding:20px; border:1px solid #ccc; z-index:9999;">
    <h2>Legitimate User</h2>
    <form id="slu-form" method="post" enctype="multipart/form-data">
        <?php wp_nonce_field('slu_form_submit','slu_nonce'); ?>
        <p>
            <label>Government Registered Name:</label>
            <input type="text" name="name" required />
        </p>
        <p>
            <label>NIC Number:</label>
            <input type="text" name="nic" required />
        </p>
        <p>
            <label>Country:</label>
            <input type="text" name="country" required />
        </p>
        <p>
            <label>Identity Verification (Passport/ID copy):</label>
            <input type="file" name="identity_verification" accept="image/*" required />
        </p>
        <p>
            <label>Financial Qualification (Proof of Funds):</label>
            <input type="file" name="financial_qualification" accept="image/*" required />
        </p>
        <p>
            <label>Buyers Confidentiality Agreement Signature (BCA):</label>
            <input type="file" name="bca" accept="image/*" required />
        </p>
        <p>
            <button type="submit">Submit</button>
        </p>
    </form>
</div>
