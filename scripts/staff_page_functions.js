function toggleMainSection(button_id, section_id) {
  var section = document.getElementById(section_id);
  var button = document.getElementById(button_id);
  // If the parameters are left empty
  if (section === null) {
    // Outputs an error message to the console
    console.log('ERROR: the \'section_id\' is invalid');
  }
  else if (button === null) {
    // Outputs an error message to the console
    console.log('ERROR: the \'button_id\' is invalid');
  }
  else {
    // Saves active sections and active buttons to an array
    var active_sections = document.getElementsByClassName('main_section_active');
    var active_buttons = document.getElementsByClassName('sidebar_button_active');
    // Selects the first element of the arrays
    var old_section = active_sections[0];
    var old_button = active_buttons[0];
    // Hides the old section and removes the active class
    old_section.style.display = 'none';
    old_section.classList.remove('main_section_active');
    // resets the background color of the old button and removes the active class
    old_button.classList.remove('sidebar_button_active');
    // Shows the new section and adds the active class
    section.style.display = 'block';
    section.classList.add('main_section_active');
    // Updates the background color of the selected button and adds the active class
    button.classList.add('sidebar_button_active');
  }
}

function loadActiveSection(section_id, button_id) {
  // Precautions in case the variables are undefined, will load the welcome page instead
  if (section_id === null || section_id === undefined || section_id === "") { section_id = 'm1'; };
  if (button_id === null || section_id === undefined || button_id === "") {button_id = 's1'; };
  // Sets the correspoding section and button to be displayed on the page
  document.getElementById(section_id).classList.add('main_section_active');
  document.getElementById(button_id).classList.add('sidebar_button_active');
  // Strips all the other sections and buttons of the active class
  toggleMainSection(button_id, section_id);
}

function displayUserDetails(user_id) {
  // POSTS the userID to a PHP form and returns the appropriate HTML code for this user
  $.ajax({
    url: '../php/sections/staff/search_user.php',
    type: 'POST',
    dataType: 'json',
    data: ({userID: user_id}),
    // Not asynchronous since we require the response in order to show the users details
    async: false,
    success: function(html) {
      // Appends the HTML code to the search user section
      $('#search_user').html(html);
      // Sets the home section to the main section
      toggleMainSection('home_button','home_section');
      // Scrolls the search user section into view
      $("#search_user")[0].scrollIntoView({ behavior: "smooth", block: "start" });
    }
  });

  $.ajax({
      url: '../php/tables/staff/recent_activity.php',
      type: 'POST',
      dataType: 'json',
      data: ({ajax: true, userid: user_id}),
      success: function(response) {
          $('#user_details_activity_table').html(response);
      }
  });
}

function triggerButton(button_id) {
  try {
    document.querySelector(button_id).click();
  }
  catch(err) {
    console.error(err);
    console.warn('Could not trigger button as the button ID provided is invalid');
  }
}

// Used to get a document cookie
function getCookie(cookieName) {
  var cookie = {};
  document.cookie.split(';').forEach(function(el) {
      var [key,value] = el.split('=');
      cookie[key.trim()] = value;
  })
  return cookie[cookieName];
}