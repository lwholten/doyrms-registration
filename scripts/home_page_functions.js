// Used to toggle the back button
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
  resetForm('user_sign_in_form');
  resetForm('user_sign_out_form');
}

// Used to toggle between the staff login form and the sign in/out forms
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

// Used to toggle between sign in and sign out forms
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

// Used to reset a form
function resetForm(formID) {
  // Resets the form passed through the parameters
  document.getElementById(formID).reset();
}

// Used to display a notification
function notification(message, type, duration) {
  // Gets the notification wrapper from the DOM and strips all children nodes
  const notification = document.getElementById('notification_wrapper');
  notification.innerHTML = '';
  // Styles the notification based on its type
  types = ['info','success','warning','error','validation'];
  type = type.toLowerCase();
  // If the notification type is allowed
  if (types.includes(type)) {
    // Iterate through the notification types and strip all type classes from the notification
    for (var i = 0; i < types.length; i++) {
      if (notification.classList.contains(types[i])) {
        // Removes the current class in the list from the element
        notification.classList.remove(types[i])
      }
      else {
        continue
      }
    }
    notification.classList.add(type);
  }
  // If the notification type is not allowed, return an error message
  else {
    console.error('Could not show notification: \''+type+'\' is not a valid notification type ');
    return;
  }
  // Formats the message and adds it to the notification
  formattedMessage = '<div class="message"><h4>'+message+'</h4></div>';
  notification.innerHTML = formattedMessage;
  // Displays the notification
  notification.style.cssText = `
    height: 80px;
    border-bottom: 1px solid;
    box-shadow: 0 -6px 10px 5px rgba(0,0,0,0.5);
  `;
  // Corrects the message duration if it is too short or too long
  if (duration > 5000) {
    console.warn('Notification duration was too long, set it to 5000ms by default');
    duration = 5000;
  }
  else if (duration < 1000) {
    console.warn('Notification duration was too long, set it to 1000ms by default');
    duration = 1000;
  }
  // Waits some time before hiding the notification
  setTimeout(function(){
    // Hides the notification
    notification.style.cssText = `
      height: 0px;
      border: 0px solid;
      box-shadow: none;
    `;
  }, duration);
}

// When the window is loaded, start the idle timer
window.onload = function() {
  idleTimer()
}

if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}
