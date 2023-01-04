// This file is used to display the idle overlay on the home page

// Hides the idle overlay
function hideOverlay() {
  // Hides the overlay
  $('#idle_overlay').css('opacity','0');
  //Makes the login box zoom in to frame
  $('#login_box').css('transform', 'scale(1.0)');
  //Execute containted code after n miliseconds (200ms)
  setTimeout(function(){ $('#idle_overlay').css('display', 'none') }, 200);
}
// Shows the idle overlay
function showOverlay() {
  // Executes the back button function, this will return the preset back to the 'in/out' preset and clear all form inputs
  backButton();
  // Shows the overlay
  $('#idle_overlay').css({
    'opacity': '1.0',
    'display': 'flex'
  });
  //Makes the login box zoom out of frame
  $('#login_box').css('transform', 'scale(0.0)');
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
