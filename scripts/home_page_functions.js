function backButton() {
  const student_selection_preset = document.getElementById('student_selection_preset');
  const staff_login_preset = document.getElementById('staff_login_preset');
  const student_sign_out_preset = document.getElementById('student_sign_out_preset');
  const student_sign_in_preset = document.getElementById('student_sign_in_preset');

  const staff_button = document.getElementById('staff_button');
  const back_button = document.getElementById('back_button');

  student_selection_preset.style.display = 'flex';
  staff_login_preset.style.display = 'none';
  student_sign_out_preset.style.display = 'none';
  student_sign_in_preset.style.display = 'none';

  staff_button.style.display = 'flex';
  back_button.style.display = 'none';
  resetForm('staff_login_form');
  resetForm('student_sign_in_form');
  resetForm('student_sign_out_form');
}

function toggleStaffLoginPreset() {
  const student_selection_preset = document.getElementById('student_selection_preset');
  const staff_login_preset = document.getElementById('staff_login_preset');

  const staff_button = document.getElementById('staff_button');
  const back_button = document.getElementById('back_button');

  student_selection_preset.style.display = 'none';
  staff_login_preset.style.display = 'flex';

  staff_button.style.display = 'none';
  back_button.style.display = 'flex';
}

function addLocations() {
  // Gets the dropdown menu that contains the signout locations
  const locations_dropdown = document.getElementById('student_sign_out_location');
  var locations = ["Location 1", "Location 2", "Location 3"];

  // If no locations are stored in the dropdown selection
  if (locations_dropdown.options.length === 0) {
    // Iterates through the available locations and adds them to the selection
    for (var i = 0; i < locations.length; i++) {
      // Note that it saves the name of the location, but keeps the value as a number, i,
      // This is to prevent a user from changing the html and uploading a false location
      locations_dropdown.options[locations_dropdown.options.length] = new Option(locations[i], i);
    };
  };
}

function toggleStudentPreset(preset) {
  const student_selection_preset = document.getElementById('student_selection_preset');
  const staff_login_preset = document.getElementById('staff_login_preset');
  const student_sign_out_preset = document.getElementById('student_sign_out_preset');
  const student_sign_in_preset = document.getElementById('student_sign_in_preset');

  const staff_button = document.getElementById('staff_button');
  const back_button = document.getElementById('back_button');

  if (preset === 'sign_in') {
    student_sign_out_preset.style.display = 'none';
    student_sign_in_preset.style.display = 'flex';
  }
  else if  (preset === 'sign_out') {
    student_sign_in_preset.style.display = 'none';
    student_sign_out_preset.style.display = 'flex';

    addLocations();
  }
  else {
    student_sign_out_preset.style.display = 'none';
    student_sign_in_preset.style.display = 'none';
    student_selection_preset.style.display = 'flex';
  }
  staff_button.style.display = 'none';
  back_button.style.display = 'flex';

  student_selection_preset.style.display = 'none';
  staff_login_preset.style.display = 'none';
}

function resetForm(formID) {
  /*Resets the form passed through the parameters*/
  document.getElementById(formID).reset();
}

function hideOverlay() {
  /*Hides the overlay*/
  overlay = document.getElementById('idle_overlay');
  overlay.style.opacity = '0';
  /*Makes the login box zoom in to frame*/
  login_box = document.getElementById('login_box');
  login_box.style.transform = 'scale(1.0)';
  /*Adds a blur effect to the background*/
  document.body.style.backdropFilter = 'blur(0px)';
  /*Execute containted code after n miliseconds (200ms)*/
  setTimeout(function(){
    overlay.style.display = 'none';
  }, 200);
}
