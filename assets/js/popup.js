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

  // // Handle form submission
  // $("#slu-form").on("submit", function (e) {
  //   e.preventDefault();
  //   var formData = new FormData(this);
  //   $.ajax({
  //     url: slu_ajax_object.ajax_url,
  //     type: "POST",
  //     data: formData,
  //     processData: false,
  //     contentType: false,
  //     success: function (response) {
  //       if (response.success) {
  //         // Replace the form content with a thank-you message
  //         $("#slu-form").html(
  //           "<p>Thanks for the submission, After the administrator review you will grant access to the bidding. Please stay tuned.</p>"
  //         );
  //       } else {
  //         alert("Error: " + response.data.message);
  //       }
  //     },
  //   });
  // });
});

jQuery(document).ready(function ($) {
  // IMAGE PREVIEW
  function sluReadURL(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function (e) {
        // remove old preview if exists
        $(input).siblings("img.slu-preview").remove();
        // insert new
        $(
          '<img class="slu-preview" src="' + e.target.result + '">'
        ).insertAfter(input);
      };
      reader.readAsDataURL(input.files[0]);
    }
  }

  // bind change on both file inputs
  $("#slu-identity, #slu-financial").on("change", function () {
    sluReadURL(this);
  });

  // OVERLAY + SPINNER + AJAX SUBMIT
  $("#slu-form").on("submit", function (e) {
    e.preventDefault();
    var $form = $(this);

    // show overlay
    var $overlay = $(
      '<div class="slu-loading-overlay"><div class="spinner"></div></div>'
    );
    $("body").append($overlay);

    // submit via AJAX (reuse your existing AJAX code)
    var formData = new FormData(this);
    $.ajax({
      url: slu_ajax_object.ajax_url,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        // remove overlay & reload
        $overlay.remove();
        if (response.success) {
          location.reload();
        } else {
          alert("Error: " + response.data.message);
        }
      },
      error: function () {
        $overlay.remove();
        alert("Unexpected error. Please try again.");
      },
    });
  });
});
