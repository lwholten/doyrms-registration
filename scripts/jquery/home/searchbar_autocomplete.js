// This file contains all of the scripts used to autocomplete text fields located on the home page

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
          url: 'autocomplete/fetch_locations.php',
          data: { term: $("#locations_input_field").val() },
					dataType: 'json',
          beforeSend: function() {
						// Removes all suggestions if the field is empty
						if (!$('#locations_input_field').val()) {
							$('#suggested_locations').empty();
						}
            document.getElementById('locations_input_field').scrollIntoView({ block: 'center',  behavior: 'smooth' });
          },
					// Note that an array of users is returned
          success: function(users) {

						// Removes all suggestions
						$('#suggested_locations').empty();

						// Appends each user as a suggestion
						for (let userIndex = 0; userIndex < users.length; userIndex++) {
							$('#suggested_locations').append("<li><h3>"+users[userIndex]+"</h3></li>");
						}

						// Adds event listeners to all the suggestions
						$('#suggested_locations').find("li").on({
							// If a suggestion is 'clicked', it will input the suggestion into the text field and hide the suggestions
							mousedown : function () {
								$('#locations_input_field').val($(this).text());
								$('#suggested_locations').css('display', 'none');
							}
						});

						// Shows the suggestions
            $('#suggested_locations').css('display', 'block');
        }
      });
		},
    // If the user clicks on the search bar, it shows the current suggestions (rather than performing another search)
    focus: function () {
      // Shows the suggestions
      $('#suggested_locations').css('display','block');
    },
    // If the user clicks away from the search bar, it hides the current suggestions
    focusout: function () {
      // Hides the suggestions after 1000ms
      setTimeout(function() {
        // If the suggestions are not hidden, hide them
        // (this is to prevent the timeout from carrying over until something else is searched, hidding the suggestions mid-search)
        if (!selected['locations']) {
          $('#suggested_locations').css('display','none');
        }
      }, 500);
    }
  });
  // Events for when a location is clicked (Hides the suggestions when one has been clicked)
  $('#suggested_locations').on({
    // When a suggestion is selected
    click : function() {
      // Hides the suggestions
      $('#suggested_locations').css('display','none');
      selected['locations']=true
      // Removes all suggestions (to prevent future searches containing old search results)
      $('#suggested_locations').empty();
      // Clears the search bar of all text
      $('#suggested_locations').val('');
    }
  });

  // Events for the sign in input field
  $('#sign_in_field').on({
    // If the user inputs any data into the search bar, a search is performed and suggestions are added and displayed
    keyup : function () {
      // Searches for all applicable user initials suggestions
      $.ajax({
          type: 'POST',
          url: 'autocomplete/fetch_users.php',
          data: { term: $("#sign_in_field").val() },
					dataType: 'json',
          beforeSend: function() {
						// Removes all suggestions if the field is empty
						if (!$('#sign_in_field').val()) {
							$('#suggested_sign_in_users').empty();
						}
            document.getElementById('sign_in_field').scrollIntoView({ block: 'center',  behavior: 'smooth' });
          },
					// Note that an array of users is returned
          success: function(users) {

						// Removes all suggestions
						$('#suggested_sign_in_users').empty();

						// Appends each user as a suggestion
						for (let userIndex = 0; userIndex < users.length; userIndex++) {
							$('#suggested_sign_in_users').append("<li onclick='selectUser(this.childNodes[0].innerHTML)'><h3>"+users[userIndex]+"</h3></li>");
						}

						// Adds event listeners to all the suggestions
						$('#suggested_sign_in_users').find("li").on({
							// If a suggestion is 'clicked', it will input the suggestion into the text field and hide the suggestions
							mousedown : function () {
								$('#sign_in_field').val($(this).text());
								$('#suggested_sign_in_users').css('display', 'none');
							}
						});

						// Shows the suggestions
            $('#suggested_sign_in_users').css('display', 'block');
        }
      });
    },
    // If the user clicks on the search bar, it shows the current suggestions (rather than performing another search)
    focus: function () {
      // Shows the suggestions
      $('#suggested_sign_in_users').css('display','block');
    },
    // If the user clicks away from the search bar, it hides the current suggestions
    focusout: function () {
      // Hides the suggestions after 1000ms
      setTimeout(function() {
        // If the suggestions are not hidden, hide them
        // (this is to prevent the timeout from carrying over until something else is searched, hidding the suggestions mid-search)
        if (!selected['signin']) {
          $('#suggested_sign_in_users').css('display','none');
        }
      }, 500);
    }
  });

  // Events for the sign out input field
  $('#sign_out_field').on({
    // If the user inputs any data into the search bar, a search is performed and suggestions are added and displayed
    keyup : function () {
      // Searches for all applicable user initials suggestions
			$.ajax({
          type: 'POST',
          url: 'autocomplete/fetch_users.php',
          data: { term: $("#sign_out_field").val() },
					dataType: 'json',
          beforeSend: function() {
						// Removes all suggestions if the field is empty
						if (!$('#sign_out_field').val()) {
							$('#suggested_sign_out_users').empty();
						}
            document.getElementById('sign_out_field').scrollIntoView({ block: 'center',  behavior: 'smooth' });
          },
					// Note that an array of users is returned
          success: function(users) {

						// Removes all suggestions
						$('#suggested_sign_out_users').empty();

						// Appends each user as a suggestion
						for (let userIndex = 0; userIndex < users.length; userIndex++) {
							$('#suggested_sign_out_users').append("<li onclick='selectUser(this.childNodes[0].innerHTML)'><h3>"+users[userIndex]+"</h3></li>");
						}

						// Adds event listeners to all the suggestions
						$('#suggested_sign_out_users').find("li").on({
							// If a suggestion is 'clicked', it will input the suggestion into the text field and hide the suggestions
							mousedown : function () {
								$('#sign_out_field').val($(this).text());
								$('#suggested_sign_out_users').css('display', 'none');
							}
						});

						// Shows the suggestions
            $('#suggested_sign_out_users').css('display', 'block');
        }
      });
    },
    // If the user clicks on the search bar, it shows the current suggestions (rather than performing another search)
    focus: function () {
      // Shows the suggestions
      $('#suggested_sign_out_users').css('display','block');
    },
    // If the user clicks away from the search bar, it hides the current suggestions
    focusout: function () {
      // Hides the suggestions after 1000ms
      setTimeout(function() {
        // If the suggestions are not hidden, hide them
        // (this is to prevent the timeout from carrying over until something else is searched, hidding the suggestions mid-search)
        if (!selected['signout']) {
          $('#suggested_sign_out_users').css('display','none');
        }
      }, 500);
    }
  });

  // An indexed two-dimensional array containing every suggestion container and its corresponding input field
  const suggestionContainers = [
    // Note: [ContainerID, corresponding input field]
    ['#suggested_locations', '#locations_input_field'],
    ['#suggested_sign_in_users', '#sign_in_field'],
    ['#suggested_sign_out_users', '#sign_out_field']
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
