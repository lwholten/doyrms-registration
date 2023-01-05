$(document).ready(function() {
  // Variables
  // The width of main must be set to (100% - the max width of the sidebar), so that the sidebars width can be set to auto
  // The width of main is then corrected after the sidebars width is calculated
  // This is because we cant perform 'width = calc(100% - auto);'
  $('#main').width('calc(100% - 300px)')
  // Stores the width of the sidebar when it is expanded and collapsed (px)
  const maxWidth = Math.round($('#sidebar').width());
  const minWidth = 50;
  // The sidebars transition duration (ms)
  const duration = parseFloat($('#sidebar').css('transition-duration')) * 1000;
  // The state of the sidebar (expanded by default)
  var collapsed = false;

  // Functions
  function expand() {
    // Sets the width of the sidebar to its maximum
    $('#sidebar').width(maxWidth+'px');
    // Adds the scrollbar (if one is required)
    $('#sidebar container').css('overflow-y', 'auto');
    // Shows the sidebars contents
    $('#sidebar container ul').css('display', 'flex');
    // Adjust the width of the main body
    $('#main').width('calc(100% - '+maxWidth+'px)');
    // After the sidebar has been expanded, make the contents visible
    setTimeout(function() { $('#sidebar container ul').css('opacity', '1.0') }, duration);
    // Stores the sidebars new state
    collapsed = false;
  }
  function collapse() {
    // Sets the width of the sidebar to its maximum
    $('#sidebar').width(minWidth+'px');
    // Removes the scrollbar (if there is one)
    $('#sidebar container').css('overflow-y', 'hidden');
    // Hides the sidebars contents (and prevents them from being clicked on by setting their display to none after the transition)
    $('#sidebar container ul').css('opacity', '0.0');
    // Adjust the width of the main body
    $('#main').width('calc(100% - '+minWidth+'px)');
    // After the sidebar has been collapsed, hide the contents (they are not visible but still there up until this point)
    setTimeout(function() { $('#sidebar container ul').css('display', 'none') }, duration);
    // Stores the sidebars new state
    collapsed = true;
  }

  // Main
  // Sets the default widths of the sidebar and main body
  $('#sidebar').width(maxWidth+'px');
  $('#main').width('calc(100% - '+maxWidth+'px)');
  // Makes the transition duration of the main body equal to the sidebar
  $('main').css('transition-duration', duration+'ms');

  // Event listeners for the sidebar toggle
  $('#sidebar_toggle').on({
    // If the toggle is clicked
    click : function () {
      // Fetches the current width of the sidebar
      var currentWidth = Math.round($('#sidebar').width());
      // Determines whether it is currently expanded or collapsed and performs the inverse of that function
      if (collapsed) {
        expand();
      } else if (!collapsed) {
        collapse();
      }
    }
  });
});
