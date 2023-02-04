// Used to toggle the back button
function backButton() {

  // Resets the displayed preset
  $('#staff_login_preset').css('display', 'none');
  $('#user_sign_out_preset').css('display', 'none');
  $('#user_sign_in_preset').css('display', 'none');
  $('#user_selection_preset').css('display', 'flex');

  // Resets the staff button
  $('#back_button').css('display', 'none');
  $('#staff_button').css('display', 'flex');

  // Resets the submit buttons
  $('.login_preset form button:last-of-type').each(function() {
    $(this).css({
      'display': 'block',
      'background': 'var(--navy)',
    });
    $(this).html('<h4>Submit</h4>')
  })

  // Resets each presets form
  $('.login_preset form').each(function() { $(this).find('input:text, input:password, input:file, select, textarea').val('') });
}

// Used to toggle between the staff login form and the sign in/out forms
function toggleStaffLoginPreset() {
  
  // Hides all other presets and displays staff preset
  $('.login_preset').each(function() {
    $(this).css('display', 'none');
  });
  $('#staff_login_preset').css('display', 'flex');

  // Displays the back button, hides the staff button
  $('#staff_button').css('display', 'none');
  $('#back_button').css('display', 'flex');
}

// Used to toggle between sign in and sign out forms
function toggleUserPreset(preset) {

  if (preset === 'sign_in') {
    // Hides the sign out preset, displays the sign in preset
    $('#user_sign_out_preset').css('display','none');
    $('#user_sign_in_preset').css('display','flex');
  }
  else if  (preset === 'sign_out') {
    // Hides the sign in preset, displays the sign out preset
    $('#user_sign_in_preset').css('display','none');
    $('#user_sign_out_preset').css('display','flex');
  }
  else {
    // Hides the sign in and out presets, displays the selection preset
    $('#user_sign_out_preset').css('display','none');
    $('#user_sign_in_preset').css('display','none');
    $('#user_selection_preset').css('display','flex');
  }

  // Displays the back button, hides the staff button
  $('#staff_button').css('display', 'none');
  $('#back_button').css('display', 'flex');

  // Hides the selection preset and staff login preset
  $('#user_selection_preset').css('display', 'none');
  $('#staff_login_preset').css('display', 'none');
}

// Used to display a notification
function notification(message, style, duration) {

  // Array of notification rypes
  notificationStyles = {
    "info"       : ['#00529B', 'rgba(189, 229, 248, 1)'],
    "success"    : ['#4F8A10', 'rgba(223, 242, 191, 1)'],
    "warning"    : ['#9F6000', 'rgb(254, 239, 179, 1)'],
    "error"      : ['#D8000C', 'rgb(255, 186, 186, 1)'],
    "validation" : ['#D63301', 'rgb(255, 204, 186, 1)'],
  }

  // Removes all content from the notification wrapper
  $('#notification_wrapper').empty();
  // Styles the notification based on its style
  
  // Sets the theme for the notification if the style is allowed
  if ( notificationStyles[style] !== undefined ) { 
    $('#notification_wrapper').css({
      'color': notificationStyles[style][0],
      'background': notificationStyles[style][1],
    }); 
  }
  // Otherwise, it returns an error
  else {
    console.error('Could not show notification: \''+style+'\' is not a valid notification type ');
    return;
  }

  // Adds the message to the notification wrapper
  $('#notification_wrapper').html('<div class="message"><h4>'+message+'</h4></div>');

  // Displays the notification
  $('#notification_wrapper').css({
    'height' : $('#main_header').height(),
    'border-botton' : '1px solid',
    'box-shadow' : '0px -6px 10px 5px rgba(0,0,0,0.5)'
  });

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
    $('#notification_wrapper').css({
      'height' : '0px',
      'border-botton' : 'none',
      'box-shadow' : 'none'
    });
  }, duration);
}

// When the window is loaded, start the idle timer
window.onload = function() {idleTimer()};
