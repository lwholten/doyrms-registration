// Functions
// Show suggestions
function showSuggestions() {
  $('#staff_searchbar_suggestions').css('display','block');
}
// Hide suggestions
function hideSuggestions() {
  $('#staff_searchbar_suggestions').css('display','none');
}
// When the user clicks on a suggested user name
function selectInitials(val) {
	$("#user_name_field").val(val.substr(5));
}

// Main
$(document).ready(function() {
  // Variables
  // Determines whether suggestions are hidden
  hidden = false;
  // The number of suggestions available (0 by default)
  suggestionCount = 0;
  // Gives the index for the suggestion that is currently selected using the arrow keys
  selectedSuggestion = 1;

  // When the enter key is pressed anywhere on the page and the user is not already using an input, focus on the search bar
  $(document).on('keypress',function(e) {
      if(e.which == 13 && !($("input").is(":focus"))) {
          $('#staff_searchbar').focus();
      }
  });

  // Functions for the various searchbar events
  $('#staff_searchbar').on({
    // If the arrow keys are pressed when the user is searching (top priority as they determine which suggestion the enter key selects)
    keydown : function (e) {
      if(e.which == 38 && selectedSuggestion > 1) {
        selectedSuggestion -= 1;
      }
      // Down arrow key (select the suggestion below the current one)
      if(e.which == 40 && selectedSuggestion < suggestionCount) {
        selectedSuggestion += 1;
      }
    },
    // If the enter key is pressed when the user is searching (this is prioritized over the keyup event)
    keypress : function (e) {
      // Enter key (search for top suggestion)
      if(e.which == 13) {
        // Simulate a 'click event' on the selected suggestion (top suggestion by default) and resets the selected suggestion
        $('#staff_searchbar_suggestions li:nth-of-type('+selectedSuggestion+')').trigger('click');
        selectedSuggestion = 1;
      }
    },
    // If the user inputs any data into the search bar, a search is performed and suggestions are added and displayed
    keyup : function () {
      // Uses Ajax to post the search term to the server and retrieve suggestions
      $.ajax({
          type: 'POST',
          url: 'autocomplete/staff_searchbar.php',
          data: { term: $('#staff_searchbar').val() },
          beforeSend: function() {
            // Removes all suggestions if the staff searchbar is empty
            if ($('#staff_searchbar').val() === '') {
              $('#staff_searchbar_suggestions').empty();
            }
          },
          success: function(data) {
            // When the suggestions are recieved, removes previous, appends new ones and displays them
            $('#staff_searchbar_suggestions').empty();
            $('#staff_searchbar_suggestions').append(data);
            $('#staff_searchbar_suggestions').css('display','block');
            // The total number of suggestions available
            suggestionCount = $("#staff_searchbar_suggestions li").length;
            // If the selected section is a value greater than the number of suggestions, it is reset
            if (selectedSuggestion > suggestionCount) {
              selectedSuggestion = 1;
            }
            // Sets the background color of the selected suggestion
            $('#staff_searchbar_suggestions:not(:hover) li:nth-of-type('+selectedSuggestion+')').css('background', 'rgba(198, 196, 200, 0.9)');
        }
      });
    },
    // If the user clicks on the search bar, it shows the current suggestions (rather than performing another search)
    focus: function () {
      // Shows the suggestions
      $('#staff_searchbar_suggestions').css('display','block');
    },
    // If the user clicks away from the search bar, it hides the current suggestions
    focusout: function () {
      // Hides the suggestions after 1000ms
      setTimeout(function() {
        // If the suggestions are not hidden, hide them
        // (this is to prevent the timeout from carrying over until something else is searched, hidding the suggestions mid-search)
        if (!hidden) {
          $('#staff_searchbar_suggestions').css('display','none');
        }
      }, 1000);
    }
  });

  // Functions performed on the searchbar suggestions
  $('#staff_searchbar_suggestions').on({
    // When a suggestion is selected
    click : function() {
      // Hides the suggestions
      $('#staff_searchbar_suggestions').css('display','none');
      hidden=true
      // Removes all suggestions (to prevent future searches containing old search results)
      $('#staff_searchbar_suggestions').empty();
      // Clears the search bar of all text
      $('#staff_searchbar').val('');
    }
  });
});
