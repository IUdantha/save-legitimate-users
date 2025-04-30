jQuery(document).ready(function ($) {
  // Simple function to check if we are on the /legitimate page.
  function isOnLegitimatePage() {
    // For a quick partial match, check if "/legitimate" is in the URL
    // Adjust if your site uses "/legitimate/" or query strings, etc.
    return window.location.pathname.includes("/legitimate");
  }

  // Function to show the modal popup
  function showModal() {
    $("#sluModal").modal("show");
  }

  var slu_form_submitted = false;
  function checkForm() {
    if (!slu_form_submitted) {
      showModal();
    }
  }

  // Only run the popup interval if NOT on the /legitimate page
  if (!isOnLegitimatePage()) {
    setInterval(function () {
      checkForm();
    }, 10000);
  }

  // Handle form submission
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
            "<p>Thanks for the submission, After the administrator review you will grant access to the bidding. Please stay tuned.</p>"
          );
        } else {
          alert("Error: " + response.data.message);
        }
      },
    });
  });
});
