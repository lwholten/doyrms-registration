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
  // Populates the overlays date container with todays details
  updateOverlayTime();
  // Shows the overlay
  $('#idle_overlay').css({
    'opacity': '1.0',
    'display': 'flex'
  });
  //Makes the login box zoom out of frame
  $('#login_box').css('transform', 'scale(0.0)');
}
// used to get the current days details
function getTodaysDetails() {
    // Array of weekdays
    const weekdays = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
    // Array of suffixes
    const suffixes = ["st", "nd", "rd", "th", "th", "th", "th", "th", "th", "th", "th", "th", "th", "th", "th", "th", "th", "th", "th", "th", "st", "nd", "rd", "th", "th", "th", "th", "th", "th", "th", "st"]
    // Array of months
    const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

    // Todays date
    const d = new Date(); 

    // An array of todays details
    today = {
      'time': ((d.getHours()<10?'0':'') + d.getHours()+':'+(d.getMinutes()<10?'0':'') + d.getMinutes()),
      'dayStr': weekdays[d.getDay()],
      'dayNum': (d.getDate()) + suffixes[d.getDate()-1],
      'month': months[d.getMonth()],
    }

    // Returns the 'today' object
    return today;
}

// Sets a 30 second timeout
function wait30(){ setTimeout('updateOverlayTime()',5000) }

function updateOverlayTime() {
  // Array of todays time features
  var today = getTodaysDetails();

  // Put the time into the idle overlays time container
  $('#idle_overlay container').html('<h1>'+today['time']+'</h1><h3>'+today['dayStr']+', '+today['dayNum']+' '+today['month']+'</h3>');
  wait30();
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
