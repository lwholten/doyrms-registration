/* This file contains all of the scripts used to autocomplete text fields*/

/* Used to display the location suggestions when a user is selecting a location*/
$(document).ready(function() {
  /* Detects when the locations input field is being used and displays locations from the database */
  $("#locations_input_field").keyup(function() {
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
  });
});

function selectLocation(val) {
	$("#locations_input_field").val(val);
	$("#location_suggestions").empty();
}

/* Used to display the name of users entering their initials*/
$(document).ready(function() {
  /* Detects when the initials have been entered and displays available names from the database */
  /* For the sign in field */
  $("#sign_in_initials_field").keyup(function() {
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
  });
  /* For the sign out field */
  $("#sign_out_initials_field").keyup(function() {
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
  });
});

/* Since this updates both the sign in and sign out initials fields, the user doesn't have to restype their name if they clicked the wrong in/out option*/
function selectInitials(val) {
	$("#sign_in_initials_field").val(val.substr(5));
  $("#sign_out_initials_field").val(val.substr(5));
	$("#sign_in_initials_suggestions").empty();
  $("#sign_out_initials_suggestions").empty();
}
