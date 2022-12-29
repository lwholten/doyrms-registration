// Used to display suggested text under the staff search bar
$(document).ready(function() {
  // Detects when text is input into the searchbar
  $("#staff_searchbar").keyup(function() {
    $.ajax({
        type: 'POST',
        url: 'autocomplete/autocomplete_staff_searchbar.php',
        data: { term: $("#staff_searchbar").val() },
        beforeSend: function() {
          // Clears the search bar if it is empty
          if ($("#staff_searchbar").val() === "") {
            $('#staff_searchbar_suggestions').empty();
          }
        },
        success: function(data) {
          $('#staff_searchbar_suggestions').empty();
          $('#staff_searchbar_suggestions').append(data);
      }
    });
  });
  // Clears the searchbar when it is not selected
  document.getElementById('staff_searchbar').addEventListener('focusout', function() {
      setTimeout(function() {
        // Clear all text from searchbar
        $('#staff_searchbar').val('');
        // Remove all list items from the suggested inputs
        $('#staff_searchbar_suggestions').empty();
      }, 200);
    });
});
