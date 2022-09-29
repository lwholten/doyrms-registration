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

  if (preset === 'log_in') {
    student_sign_out_preset.style.display = 'none';
    student_sign_in_preset.style.display = 'flex';
  }
  else if  (preset === 'log_out') {
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

function toggleFooter() {
  const footer_expanded_preset = document.getElementById('footer_expanded_preset');
  const footer_collapsed_preset = document.getElementById('footer_collapsed_preset');

  /*If the footer has been collapsed*/
  if (footer_collapsed_preset.style.backgroundColor === 'var(--burgundy)') {
    footer_collapsed_preset.style.backgroundColor = 'var(--navy)';
    footer_expanded_preset.style.height =  '1px';
  }
  /*If the footer has been expanded*/
  else {
    footer_collapsed_preset.style.backgroundColor = 'var(--burgundy)';
    footer_expanded_preset.style.height =  '120px';
  }

}

function copyContactEmail() {
  /*saves the content within the element*/
  var content = document.getElementById("contact_email").innerHTML;
  var email = '';
  /*Iterates through the content and appends it to the email*/
  for (var i = 0; i < content.length; i++) {
    character = content.charAt(i);
    /*If the character is invalid, the loop breaks and the email is returned*/
    if (character !== '<') {
      email += character
    }
    else {
      break
    }
  }
  navigator.clipboard.writeText(email);
  window.alert('Copied email to clipboard')
}
