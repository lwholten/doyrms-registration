// This file contains all of the scripts used to autocomplete text fields located on the home page

// Function to search database for current value(s)
function locationSearch() {
  $.ajax({
      type: 'POST',
      url: 'autocomplete/autocomplete_locations_input_field.php',
      data: { term: $("#locations_input_field").val() },
      beforeSend: function() {
        document.getElementById('locations_input_field').scrollIntoView({ block: 'center',  behavior: 'smooth' });
      },
      success: function(data) {
        $('#location_suggestions').empty();
        $('#location_suggestions').append(data);
    }
  });
}
// Function to search database for current sign in value(s)
function signInSearch() {
  $.ajax({
      type: 'POST',
      url: 'autocomplete/autocomplete_initials_field.php',
      data: { term: $("#sign_in_initials_field").val() },
      beforeSend: function() {
        document.getElementById('sign_in_initials_field').scrollIntoView({ block: 'center',  behavior: 'smooth' });
      },
      success: function(data) {
        $('#sign_in_initials_suggestions').empty();
        $('#sign_in_initials_suggestions').append(data);
    }
  });
}
// Function to search database for current sign out value(s)
function signOutSearch() {
  $.ajax({
      type: 'POST',
      url: 'autocomplete/autocomplete_initials_field.php',
      data: { term: $("#sign_out_initials_field").val() },
      beforeSend: function() {
        document.getElementById('sign_out_initials_field').scrollIntoView({ block: 'center',  behavior: 'smooth' });
      },
      success: function(data) {
        $('#sign_out_initials_suggestions').empty();
        $('#sign_out_initials_suggestions').append(data);
    }
  });
}
// Function used when the user clicks on a suggested location
function selectLocation(val) {
	$("#locations_input_field").val(val);
	$("#location_suggestions").empty();
}
// Function used when the user clicks on suggested user inputs
function selectInitials(val) {
	$("#sign_in_initials_field").val(val.substr(5));
  $("#sign_out_initials_field").val(val.substr(5));
	$("#sign_in_initials_suggestions").empty();
  $("#sign_out_initials_suggestions").empty();
}

// Used to display the location suggestions when a user is selecting a location
$(document).ready(function() {
  // Detects when the locations is being input and displays locations from the database
  $("#locations_input_field").keyup(function() {locationSearch();});
  $("#locations_input_field").focus(function() {locationSearch();});
  // Detects when the sign out field is in use and outputs user suggestions from the database
  $("#sign_in_initials_field").keyup(function() {signInSearch();});
  $("#sign_in_initials_field").focus(function() {signInSearch();});
  // Detects when the sign out field is in use and outputs user suggestions from the database
  $("#sign_out_initials_field").keyup(function() {signOutSearch();});
  $("#sign_out_initials_field").focus(function() {signOutSearch();});

  // Sign out field
  document.getElementById('sign_out_initials_field').addEventListener('focusout', function() {
      // Remove all list items from the suggested inputs after a 50ms delay
      // This is to allow a 'click' to be registered if the user clicked a suggested value
      setTimeout(function() {
          $('#sign_out_initials_suggestions').empty();
      }, 200);
    });
  // Sign in field
  document.getElementById('sign_in_initials_field').addEventListener('focusout', function() {
      // Remove all list items from the suggested inputs after a 50ms delay
      setTimeout(function() {
          $('#sign_in_initials_suggestions').empty();
      }, 200);
    });
  // Locations field
  document.getElementById('locations_input_field').addEventListener('focusout', function() {
      // Remove all list items from the suggested inputs after a 50ms delay
      setTimeout(function() {
          $('#location_suggestions').empty();
      }, 200);
    });
});
