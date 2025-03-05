jQuery(document).ready(function ($) {
  // Function to show the Bootstrap modal popup
  function showModal() {
    $("#sluModal").modal("show");
  }

  var slu_form_submitted = false;
  function checkForm() {
    if (!slu_form_submitted) {
      showModal();
    }
  }

  // Trigger the popup every 10 seconds if the form isn’t submitted
  setInterval(function () {
    checkForm();
  }, 10000);

  $("#slu-form").on("submit", function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
      url: slu_ajax_object.ajax_url,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if (response.success) {
          // Replace the form content with a thank-you message
          $("#slu-form").html(
            "<p>Thanks for the submission, After the administrator review you will grand access to the bidding. Please stay tuned.</p>"
          );
        } else {
          alert("Error: " + response.data.message);
        }
      },
    });
  });
});
