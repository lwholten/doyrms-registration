// This file is used to update the 'dynamic background' css variable every hour
// The 'dynamic_background' variable is set based on the current time

// Array of all possible backgrounds
backgrounds = [
  'linear-gradient(#012459 0%, #001322 100%)',
  'linear-gradient(#012459 0%, #001323 100%)',
  'linear-gradient(#003972 0%, #001322 100%)',
  'linear-gradient(#004372 0%, #00182b 100%)',
  'linear-gradient(#004372 0%, #011d34 100%)',
  'linear-gradient(#016792 0%, #00182b 100%)',
  'linear-gradient(#07729f 0%, #042c47 100%)',
  'linear-gradient(#12a1c0 0%, #07506e 100%)',
  'linear-gradient(#74d4cc 0%, #1386a6 100%)',
  'linear-gradient(#efeebc 0%, #61d0cf 100%)',
  'linear-gradient(#fee154 0%, #a3dec6 100%)',
  'linear-gradient(#fdc352 0%, #e8ed92 100%)',
  'linear-gradient(#ffac6f 0%, #ffe467 100%)',
  'linear-gradient(#fda65a 0%, #ffe467 100%)',
  'linear-gradient(#fd9e58 0%, #ffe467 100%)',
  'linear-gradient(#f18448 0%, #ffd364 100%)',
  'linear-gradient(#f06b7e 0%, #f9a856 100%)',
  'linear-gradient(#ca5a92 0%, #f4896b 100%)',
  'linear-gradient(#5b2c83 0%, #d1628b 100%)',
  'linear-gradient(#371a79 0%, #713684 100%)',
  'linear-gradient(#28166b 0%, #45217c 100%)',
  'linear-gradient(#192861 0%, #372074 100%)',
  'linear-gradient(#040b3c 0%, #233072 100%)',
  'linear-gradient(#040b3c 0%, #012459 100%)',
];
// Function used to update the background to the background represented by the current hour
function updateBackground() {
  // Gets the current hour
  var currentHour = new Date().getHours();
  var currentBackground = getComputedStyle(document.documentElement).getPropertyValue('--dynamic_background');

  // If this background has already been set, pass (this stops it from refreshing after each time interval)
  if (currentBackground === backgrounds[currentHour-1]) {
    return
  }
  // Else, update tha background
  else {
    // Allows the background to be set during midnight, since at midnight hours is set to 0 and must be 24 instead
    if (currentHour === 0) {
      currentHour = 24;
    }
    // Sets the css variable 'dynamic_background' to the backgrounds array indexed with the current hour
    document.documentElement.style.setProperty('--dynamic_background', backgrounds[currentHour-1]);
  }
}

// Code executed when the document is loaded
$(document).ready(function(){
  // Contained code is executed after every time interval
  function timeout() {
    updateBackground();
    setTimeout(timeout, 60000);
  }

  // Executes once the document is loaded, then repeats at regular intervals
  timeout();
});
