jQuery(document).ready(function ($) {
  // Open the edit modal when an Edit button is clicked.
  $(".edit-entry").on("click", function () {
    var entryId = $(this).data("id");
    // Optionally, extract the current row’s data (or do an AJAX call for details).
    var row = $(this).closest("tr");
    var name = row.find("td:nth-child(3)").text();
    var nic = row.find("td:nth-child(4)").text();
    var country = row.find("td:nth-child(5)").text();
    var status = row.find("td:nth-child(10)").text();

    $("#entry_id").val(entryId);
    $("#edit-name").val(name);
    $("#edit-nic").val(nic);
    $("#edit-country").val(country);
    $("#edit-status").val(status.trim());

    $("#editEntryModal").modal("show");
  });

  // Submit the edit form via AJAX.
  $("#editEntryForm").on("submit", function (e) {
    e.preventDefault();
    var formData = $(this).serialize();

    // 1) Show a full-screen overlay with Bootstrap spinner
    var $overlay = $(
      '<div id="slu-admin-overlay" style="position:fixed;top:0;left:0;width:100%;height:100%;' +
        "background:rgba(0,0,0,0.5);z-index:10000;display:flex;align-items:center;" +
        'justify-content:center;">' +
        '<div class="spinner-border text-light" role="status"><span class="sr-only">Loading...</span></div>' +
        "</div>"
    );
    $("body").append($overlay);

    // 2) Fire the AJAX
    $.post(
      slu_admin_ajax_object.ajax_url,
      formData + "&action=slu_edit_entry",
      function (response) {
        // Remove overlay
        $("#slu-admin-overlay").remove();

        if (response.success) {
          alert(response.data.message);
          location.reload();
        } else {
          alert("Error: " + response.data.message);
        }
      }
    ).fail(function () {
      // On failure also remove overlay
      $("#slu-admin-overlay").remove();
      alert("Unexpected error. Please try again.");
    });
  });

  // Handle delete action.
  $(".delete-entry").on("click", function () {
    if (confirm("Are you sure you want to delete this entry?")) {
      var entryId = $(this).data("id");
      $.post(
        slu_admin_ajax_object.ajax_url,
        { action: "slu_delete_entry", entry_id: entryId },
        function (response) {
          if (response.success) {
            alert(response.data.message);
            location.reload();
          } else {
            alert("Error: " + response.data.message);
          }
        }
      );
    }
  });
});
