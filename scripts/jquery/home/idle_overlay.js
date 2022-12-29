// This file is used to display the idle overlay on the home page

// Hides the idle overlay
function hideOverlay() {
  overlay = document.getElementById('idle_overlay');
  overlay.style.opacity = '0';
  //Makes the login box zoom in to frame
  login_box = document.getElementById('login_box');
  login_box.style.transform = 'scale(1.0)';
  //Execute containted code after n miliseconds (200ms)
  setTimeout(function(){
    overlay.style.display = 'none';
  }, 200);
}
// Shows the idle overlay
function showOverlay() {
  overlay = document.getElementById('idle_overlay');
  overlay.style.display = 'flex';
  overlay.style.opacity = '1.0';
  //Makes the login box zoom out of frame
  login_box = document.getElementById('login_box');
  login_box.style.transform = 'scale(0.0)';
}

// When the document is loaded
$(document).ready(function(){
  // Timer used to determine idle time
  function idleTimer() {
    var time;
    //Resets the timer when the window or document is loaded
    window.onload = resetTimer;
    document.onload = resetTimer;

    //Reset the timer when one of these occur
    document.onmousemove = resetTimer;
    document.onmousedown = resetTimer;
    document.onkeydown = resetTimer;
    document.onclick = resetTimer;

    //Used to reset the timer, also shows the overlay when this occurs
    function resetTimer() {
        clearTimeout(time);
        time = setTimeout(showOverlay, 30000)
        //Note 1 second = 1000ms
    }
  }

  // Starts the idle timer
  idleTimer()

});
