// Variables
// Duration of fadein/out animations (ms)
animationDuration = 250;
// The overlays current state
popupActive = false;

// An object used to contain the paths used for the popup HTML files
popupHTMLPaths = {
  // Staff
  signout  : '../../pages/html/popups/staff/sign_user_out.html',
  signin   : '../../pages/html/popups/staff/sign_user_in.html',
  download : '../../pages/html/popups/staff/download_data.html',
  restrict : '../../pages/html/popups/staff/restrict_user.html',
  unrestrict : '../../pages/html/popups/staff/unrestrict_user.html',
  markaway : '../../pages/html/popups/staff/mark_user_away.html',
  markpresent : '../../pages/html/popups/staff/mark_user_present.html',
  staffpassword : '../../pages/html/popups/staff/change_staff_password.html',
  changepasswordprompt: '../../pages/html/popups/staff/change_password_prompt.html',

  // Admin
  addstaff : '../../pages/html/popups/admin/add_staff.html',
  removestaff : '../../pages/html/popups/admin/remove_staff.html',
  adduser : '../../pages/html/popups/admin/add_user.html',
  removeuser : '../../pages/html/popups/admin/remove_user.html',
  addlocation : '../../pages/html/popups/admin/add_location.html',
  removelocation : '../../pages/html/popups/admin/remove_location.html',
  addevent : '../../pages/html/popups/admin/add_event.html',
  removeevent : '../../pages/html/popups/admin/remove_event.html',
}
// An object used to contain the form IDs and their file path for each popup forms action file
formActionPaths = {
  // Staff
  sign_user_in_form : '../../pages/php/forms/staff/sign_user_in.php',
  sign_user_out_form : '../../pages/php/forms/staff/sign_user_out.php',
  restrict_user_form : '../../pages/php/forms/staff/restrict_user.php',
  unrestrict_user_form : '../../pages/php/forms/staff/unrestrict_user.php',
  mark_user_away_form : '../../pages/php/forms/staff/mark_user_away.php',
  mark_user_present_form : '../../pages/php/forms/staff/mark_user_present.php',
  change_staff_password_form : '../../pages/php/forms/staff/change_staff_password.php',
  // The only difference between the change password prompt and the regular change password is the HTML form
  change_password_prompt_form: '../../pages/php/forms/staff/change_staff_password.php',

  // Admin
  add_staff_form : '../../pages/php/forms/admin/add_staff.php',
  remove_staff_form : '../../pages/php/forms/admin/remove_staff.php',
  add_user_form : '../../pages/php/forms/admin/add_user.php',
  remove_user_form : '../../pages/php/forms/admin/remove_user.php',
  add_location_form : '../../pages/php/forms/admin/add_location.php',
  remove_location_form : '../../pages/php/forms/admin/remove_location.php',
  add_event_form : '../../pages/php/forms/admin/add_event.php',
  remove_event_form : '../../pages/php/forms/admin/remove_event.php',
}

// Functions
// Removes the popup
function removePopup() {
  // Only triggers if a popup is active
  if (popupActive) {
    // Animates the popup 'fading into view'
    $('#staff_overlay').fadeOut(animationDuration).css('display','flex');
    // Animates the card 'falling into place'
    $('#staff_overlay .card').css({
      // Declares the duration
      'transition-duration': animationDuration+'ms',
      'transform': 'translateY(-100px)'
    });
    // Waits for the animation to be complete before removing the overlays innerHTML
    setTimeout(function() { $('#staff_overlay').html('') }, animationDuration);
    popupActive = false;
  }
}
// Used to add event listeners to the fields contained within a popup
function addEventListeners() {

  // Event listener for if text is input into the form
  $('#staff_overlay .card form:first-of-type').keydown(function(e) {

    const submitButton = $('#staff_overlay .card form:first-of-type button:last-of-type');
    const errorWrapper = $('#staff_overlay .card form .error_wrapper');
    const infoWrapper = $('#staff_overlay .card form .tooltip_wrapper');

    // If the enter key was not pressed and the form is not loading
    if (e.which != 13 && !$(submitButton).hasClass('loading')) {

      // Removes any error messages
      $(errorWrapper).css('display', 'none');
      $(errorWrapper).empty();

      // Reset the submit button
      $(submitButton).css('background', 'var(--navy)');
      $(submitButton).html('<h4>Submit</h4>');
    }
  });

  // Event listener for when the form is submitted
  $('#staff_overlay .card form:first-of-type').submit(function(e) {

    e.preventDefault(); // Do not execute the actual form submit

    // Indexes the object that stores all the action file paths with the forms ID
    var actionPath = formActionPaths[$(this).attr('id')];
    // Submit button
    const submitButton = $('#staff_overlay .card form:first-of-type button:last-of-type');
    const errorWrapper = $('#staff_overlay .card form .error_wrapper');
    const infoWrapper = $('#staff_overlay .card form .tooltip_wrapper');

    $.ajax({
        type: "POST",
        url: actionPath,
        timeout: 5000,
        data: $(this).serialize() + "&staff_action=1", // Serializes the form's elements (and states that this was a staff action)
        dataType: 'json',
        beforeSend: function() {
          // Hides any info messages
          $(infoWrapper).css('display', 'none');

          // Removes any error messages
          $(errorWrapper).css('display', 'none');
          $(errorWrapper).empty();

          // Animates the buttons 'loading' state
          $(submitButton).empty();
          $(submitButton).addClass('loading');

        }
    }).done(function(data) {

      // Changes the buttons state to successful
      $(submitButton).css('background', 'var(--green)');
      $(submitButton).html('<h4>Done</h4>');

      removePopup();

    }).fail(function(jqXHR, status, error) {

      // Hides any info messages
      $(infoWrapper).css('display', 'none');

      // Changes the buttons state to failure
      $(submitButton).css('background', 'var(--red)');
      $(submitButton).html('<h4>Failure</h4>');

      // Displays an error message
      $(errorWrapper).css('display', 'block');
      $(errorWrapper).text(error);

    }).always(function() {

      // Displays the submit button and removes the spinner
      $(submitButton).css('display', 'block');
      $(submitButton).removeClass('loading');

    });

  });

  // Event listeners for the close button
  $('#staff_overlay .card header .close').on({
    // Applies the background when hovering
    mouseover : function() {$(this).css('background', 'var(--light-grey)')},
    // Removes the background when not hovering
    mouseout : function() {$(this).css('background', 'none')},
    // Removes the popup when pressed
    click : function () {removePopup()}
  });

  // Event listeners for the info button
  $('#staff_overlay .card header .info').on({
    // Applies the background when hovering
    mouseover : function() {$(this).css('background', 'var(--light-grey)')},
    // Removes the background when not hovering
    mouseout : function() {$(this).css('background', 'none')},
    // Displays the tooltip section and hides the error section when clicked
    click : function () {
      $('#staff_overlay .card form .error_wrapper').css('display', 'none');
      $('#staff_overlay .card form .tooltip_wrapper').css('display', 'block');
      // Resets the submit button
      $('#staff_overlay .card form:first-of-type button:last-of-type').css('background', 'var(--navy)');
      $('#staff_overlay .card form:first-of-type button:last-of-type').html('<h4>Submit</h4>');
    }
  });

  // Event listeners for time inputs
  $('#staff_overlay form').find('input[type=time]').each(function() {
    // Sets the placeholder to 'optional' when the input is loaded
    if ($(this).attr('data-placeholder') == 'required') {
      $(this).attr('data-before', 'Required');
    }
    else {
      $(this).attr('data-before', 'Optional');
    }

    // Removes the placeholder if a time is selected
    $(this).on('input', function() {$(this).attr('data-before', '')});
  })

  // Event listeners for date inputs
  $('#staff_overlay form').find('input[type=date]').each(function() {
    // Sets the placeholder to 'optional' when the input is loaded
    if ($(this).attr('data-placeholder') == 'required') {
      $(this).attr('data-before', 'Required');
    }
    else {
      $(this).attr('data-before', 'Optional');
    }

    // Removes the placeholder if a date is selected
    $(this).on('input', function() {$(this).attr('data-before', '')});
  })

  // Event listeners for event text input (used to show the lateness slider if an event is selected)
  $('#staff_overlay form').find('input[type=text][name=event_field]').each(function() {
    $(this).on({
      // If a value is input into the event field
      input : function() {
        if ($(this).val()) {
          // Display the event timing elements
          $(this).parent().next().next('.event_timing_wrapper').css('display', 'block');
        }
        else {
          // Hides the event timing elements
          $(this).parent().next().next('.event_timing_wrapper').css('display', 'none');
        }
      }
    });
  });

  // Event listeners for event range inputs
  $('#staff_overlay form').find('input[type=range][name=event_timing]').each(function() {
    // Detects whether a value is selected
    $(this).on({
      input : function() {
        sliderVal = $(this).val();
        // If the value is 0 display 'on time'
        if (sliderVal == 0) {
          $(this).next('output').val('On Time');
        }
        // If the vlaue is greater than zero, assume late
        else if (sliderVal > 0) {
          $(this).next('output').val(sliderVal+' Minutes Late');
        }
        // If the vlaue is less than zero, assume early
        else if (sliderVal < 0) {
          $(this).next('output').val(Math.abs(sliderVal)+' Minutes Early');
        }
        // Otherwise just return the value like normal
        else {
          $(this).next('output').val(sliderVal);
        }
      }
    });
  });

  // Function used to add the required event listeners to a given field and its children
  // This is used primarily to provide input suggestions below a field and autocompleting the field when a suggestion is clicked
  function addFieldEventListeners(field) {
    // Fetches the 'nature' of the element as denoted by a data attribute in the HTML
    field.nature = $(field.fieldID).data('nature');

    // Event listeners for the field
    $(field.fieldID).on({

      // If the user presses the TAB key, select the top suggestion and move on to the next field
      keydown : function (e) {
        // 9 -> TAB key
        if(e.which == 9) {
          // Simulate a 'click event' on the top suggestion
          $(field.fieldID).next('.suggested_inputs').find("li:first-of-type").trigger('mousedown');
          // Clear and hide the suggestions
          $(field.fieldID).next('.suggested_inputs').empty();
          $(field.fieldID).next('.suggested_inputs').css('display', 'none');
          // Focuses on the next input
          $(this).next().focus();
        }
      },

      // If the user inputs any data into the field
      keyup : function (e) {
        // If the keys pressed are not the TAB key
        if (e.which != 9) {
          // Perform a search and return an array of suggestions
          $.ajax({
              type: 'POST',
              url: field.ajaxPath,
              data: { term: $(this).val(), nature: field.nature },
    					dataType: 'json',
              beforeSend: function() {

    						// Removes all suggestions if the field is empty
    						if (!$(field.fieldID).val()) {
    							$(field.fieldID).next('.suggested_inputs').empty();
    						}

              },
              success: function(suggestions) {

    						// Removes all pre-existing suggestions
    						$(field.fieldID).next('.suggested_inputs').empty();

    						// Appends each suggestion to the suggestions list (ul)
    						for (let i = 0; i < suggestions.length; i++) {$(field.fieldID).next('.suggested_inputs').append("<li>"+suggestions[i]+"</li>")}

                // Adds event listeners to all the suggestions
                $(field.fieldID).next('.suggested_inputs').find("li").on({
                  // If a suggestion is 'clicked', it will input the suggestion into the text field and hide the suggestions
                  mousedown : function () {
                    $(field.fieldID).val($(this).text());
                    $(field.fieldID).next('.suggested_inputs').css('display', 'none');
                  }
                });

    						// Shows the suggestions
                $(field.fieldID).next('.suggested_inputs').css('display', 'block');
              } // End of success

            }); // End of Ajax

          } // End of IF statement

        }, // End of keyup

      // If the user clicks on the search bar, it shows the current suggestions (rather than performing another search)
      focus: function () {$(field.fieldID).next('.suggested_inputs').css('display','block')},
      // If the user clicks away from the search bar, it hides the current suggestions
      focusout: function () {$(field.fieldID).next('.suggested_inputs').css('display','none')}

    });
  } // End of function

  // Array to contain the various field types as objects
  const fieldTypes = {};
  // Object to contain the data associated with the locations field and its autocompleted suggestions
  fieldTypes['locations'] = {fieldID:'#location_field', ajaxPath:'autocomplete/fetch_locations.php', nature:null},
  // Object to contain the data associated with the user name field and its autocompleted suggestions
  fieldTypes['users'] = {fieldID:'#name_field', ajaxPath:'autocomplete/fetch_users.php', nature:null}
  // Object to contain the data associated with the user events field and its autocompleted suggestions
  fieldTypes['events'] = {fieldID:'#event_field', ajaxPath:'autocomplete/fetch_events.php', nature:null}
  // Object to contain the data associated with the staff username field and its autocompleted suggestions
  fieldTypes['staff'] = {fieldID:'#staff_username_field', ajaxPath:'autocomplete/fetch_staff.php', nature:null}

  // For every field type add its required event listeners
  for (var field in fieldTypes) { addFieldEventListeners(fieldTypes[field]); }

}
// Used to trigger a popup, 'type' determines its nature
function triggerPopup(type) {
  // Only triggers if a popup is not already active
  if (!popupActive) {

    // Gets the HTML code required for this type of popup using an Ajax GET request
    $.ajax({
      url: popupHTMLPaths[type], // Note that the file is determined by the 'type' of popup
      type: 'get',
      async: false,
      success: function(html) {
        // Inserts the code into the popup
        $('#staff_overlay').html(html);
      }
    });

    // Animates the popup 'fading into view'
    $('#staff_overlay').fadeIn(animationDuration).css('display','flex');
    // Animates the card 'falling into place'
    $('#staff_overlay .card').css({
      'transition-duration': animationDuration+'ms',
      'transform': 'translateY(0px)'
    });
    // Focus the first input field in the popup
    $('#staff_overlay form :input:enabled:visible:first'). focus();

    // Add the event listeners for the elements contained within the popup
    // This allows the autocomplete functions of some like 'users' to work
    addEventListeners();
    // Declare that a popup is active
    popupActive = true;
  }
}

// Main
$(document).ready(function () {
  // Hide the overlay popup if the escape key is pressed (only executes if the change password prompt is set to false)
  $(document).on('keydown', function(e) {
    if(e.key == "Escape" && popupActive && !(getCookie('dreg_changePasswordPrompt') == 1)) {
      // Hide the current overlay
      removePopup();
    }
  });
  // Event listeners for an overlay trigger
  $('.overlay_trigger').on({
    // If an overlay trigger is clicked
    click : function() {
      // Trigger a popup (its nature is defined by the elements 'trigger type')
      triggerPopup($(this).data('trigger-type'));
    }
  });
});

$(window).on('load', function () {
  // If the cookie used to determine whether a password change is required is set to true, trigger the 'change password' prompt
  if (getCookie('dreg_changePasswordPrompt') == 1) {triggerPopup('changepasswordprompt');}
});
