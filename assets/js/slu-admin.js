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
    $.post(
      slu_admin_ajax_object.ajax_url,
      formData + "&action=slu_edit_entry",
      function (response) {
        if (response.success) {
          alert(response.data.message);
          location.reload();
        } else {
          alert("Error: " + response.data.message);
        }
      }
    );
  });

  // Handle delete action.
  $(".delete-entry").on("click", function () {
    if (confirm("Are you sure you want to delete this entry?")) {
      var entryId = $(this).data("id");
      $.post(
        slu_admin_ajax_object.ajax_url,
        {action: "slu_delete_entry", entry_id: entryId},
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
