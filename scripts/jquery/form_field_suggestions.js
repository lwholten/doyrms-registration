// This file contains code used to get the dropdown suggestions for form input fields
$(document).ready(function() {
  
  // Function used to add the required event listeners to a given field and its children
  // This is used primarily to provide input suggestions below a field and autocompleting the field when a suggestion is clicked
  // Note that the wrapper for the suggested inputs is assumed to be the next unordered list with the class '.suggested_inputs'
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
    						$(field.fieldID).next('ul').empty();

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

  // Home page unique
  // The name field for the users sign out form
  fieldTypes['users_sign_out'] = {fieldID:'#user_self_sign_out_form input[name=name_field][type=text]', ajaxPath:'autocomplete/fetch_users.php', nature:null};
  // The location field for the users sign out  form
  fieldTypes['users_location'] = {fieldID:'#user_self_sign_out_form input[name=location_field][type=text]', ajaxPath:'autocomplete/fetch_locations.php', nature:null};
  // The name field for the users sign in form
  fieldTypes['users_sign_in'] = {fieldID:'#user_self_sign_in_form input[name=name_field][type=text]', ajaxPath:'autocomplete/fetch_users.php', nature:null};

  // For every field type add its required event listeners
  for (var field in fieldTypes) { addFieldEventListeners(fieldTypes[field]);};

});