// This file contains all of the scripts used to autocomplete text fields located on the home page

// Functions
// When the user clicks on a suggested location
function selectLocation(val) {
	$("#locations_input_field").val(val);
}
// When the user clicks on suggested users
function selectInitials(val) {
	$("#sign_in_initials_field").val(val.substr(5));
  $("#sign_out_initials_field").val(val.substr(5));
}

// Main
$(document).ready(function() {
  // Variables
  // Associative array used to cache whether suggestions have been hidden manually (by being clicked on)
  var selected = new Map([
   ['locations', false],
   ['signin', false],
   ['signout', false],
 ]);

  // Events for the locations input field
  $('#locations_input_field').on({
    // If the user inputs any data into the search bar, a search is performed and suggestions are added and displayed
    keyup : function () {
      // Searches for all applicable location suggestions
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
            $('#location_suggestions').css('display', 'block');
        }
      });
    },
    // If the user clicks on the search bar, it shows the current suggestions (rather than performing another search)
    focus: function () {
      // Shows the suggestions
      $('#location_suggestions').css('display','block');
    },
    // If the user clicks away from the search bar, it hides the current suggestions
    focusout: function () {
      // Hides the suggestions after 1000ms
      setTimeout(function() {
        // If the suggestions are not hidden, hide them
        // (this is to prevent the timeout from carrying over until something else is searched, hidding the suggestions mid-search)
        if (!selected['locations']) {
          $('#location_suggestions').css('display','none');
        }
      }, 500);
    }
  });
  // Events for when a location is clicked (Hides the suggestions when one has been clicked)
  $('#location_suggestions').on({
    // When a suggestion is selected
    click : function() {
      // Hides the suggestions
      $('#location_suggestions').css('display','none');
      selected['locations']=true
      // Removes all suggestions (to prevent future searches containing old search results)
      $('#location_suggestions').empty();
      // Clears the search bar of all text
      $('#location_suggestions').val('');
    }
  });

  // Events for the sign in input field
  $('#sign_in_initials_field').on({
    // If the user inputs any data into the search bar, a search is performed and suggestions are added and displayed
    keyup : function () {
      // Searches for all applicable user initials suggestions
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
            $('#sign_in_initials_suggestions').css('display', 'block');
        }
      });
    },
    // If the user clicks on the search bar, it shows the current suggestions (rather than performing another search)
    focus: function () {
      // Shows the suggestions
      $('#sign_in_initials_suggestions').css('display','block');
    },
    // If the user clicks away from the search bar, it hides the current suggestions
    focusout: function () {
      // Hides the suggestions after 1000ms
      setTimeout(function() {
        // If the suggestions are not hidden, hide them
        // (this is to prevent the timeout from carrying over until something else is searched, hidding the suggestions mid-search)
        if (!selected['signin']) {
          $('#sign_in_initials_suggestions').css('display','none');
        }
      }, 500);
    }
  });

  // Events for the sign out input field
  $('#sign_out_initials_field').on({
    // If the user inputs any data into the search bar, a search is performed and suggestions are added and displayed
    keyup : function () {
      // Searches for all applicable user initials suggestions
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
            $('#sign_out_initials_suggestions').css('display', 'block');
        }
      });
    },
    // If the user clicks on the search bar, it shows the current suggestions (rather than performing another search)
    focus: function () {
      // Shows the suggestions
      $('#sign_out_initials_suggestions').css('display','block');
    },
    // If the user clicks away from the search bar, it hides the current suggestions
    focusout: function () {
      // Hides the suggestions after 1000ms
      setTimeout(function() {
        // If the suggestions are not hidden, hide them
        // (this is to prevent the timeout from carrying over until something else is searched, hidding the suggestions mid-search)
        if (!selected['signout']) {
          $('#sign_out_initials_suggestions').css('display','none');
        }
      }, 500);
    }
  });

  // An indexed two-dimensional array containing every suggestion container and its corresponding input field
  const suggestionContainers = [
    // Note: [ContainerID, corresponding input field]
    ['#location_suggestions', '#locations_input_field'],
    ['#sign_in_initials_suggestions', '#sign_in_initials_field'],
    ['#sign_out_initials_suggestions', '#sign_out_initials_field']
  ]
  // For each suggestion container, if a suggestion is selected it hides all the suggestions
  for (let i = 0; i < suggestionContainers.length; i++) {
    // Events for when a suggestion is clicked
    $(suggestionContainers[i][0]).on({
      // When a suggestion is selected
      click : function() {
        // Hides the suggestions
        $(suggestionContainers[i][0]).css('display','none');
        selected['locations']=true;
        // Removes all suggestions (to prevent future searches containing old search results)
        $(suggestionContainers[i][0]).empty();
      }
    });
  }
});
