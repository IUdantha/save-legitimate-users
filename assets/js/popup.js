jQuery(document).ready(function ($) {
  // Function to show the modal popup
  function showModal() {
    $("#slu-modal").fadeIn();
  }

  // Check if the form is not submitted yet
  // (For a real implementation, you might want to check via an AJAX call or a flag)
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

  // Handle the AJAX form submission
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
          alert("Form submitted successfully");
          slu_form_submitted = true;
          $("#slu-modal").fadeOut();
        } else {
          alert("Error: " + response.data.message);
        }
      },
    });
  });
});
