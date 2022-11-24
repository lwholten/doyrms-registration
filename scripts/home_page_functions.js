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
  /*Execute containted code after n miliseconds (200ms)*/
  setTimeout(function(){
    overlay.style.display = 'none';
  }, 200);
}
function showOverlay() {
  /*Shows the overlay*/
  overlay = document.getElementById('idle_overlay');
  overlay.style.display = 'flex';
  overlay.style.opacity = '1.0';
  /*Makes the login box zoom out of frame*/
  login_box = document.getElementById('login_box');
  login_box.style.transform = 'scale(0.0)';
}
function idleTimer() {
  var time;
  /*Resets the timer when the window or document is loaded*/
  window.onload = resetTimer;
  document.onload = resetTimer;

  /*Reset the timer when one of these occur*/
  document.onmousemove = resetTimer;
  document.onmousedown = resetTimer;
  document.onkeydown = resetTimer;
  document.onclick = resetTimer;

  /*Used to reset the timer, also shows the overlay when this occurs*/
  function resetTimer() {
      clearTimeout(time);
      time = setTimeout(showOverlay, 30000)
      /*Note 1 second = 1000ms*/
  }
}

/*When the window is loaded, start the idle timer*/
window.onload = function() {
  idleTimer()
}

if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}
