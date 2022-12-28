<?php
function loadUserDetails() {
  // Connects to the database
  $con = new mysqli('localhost', 'dreg_user', 'epq', 'dregDB');
  // Code used to get all users, signed in, signed out, away and restricted
  $sql = 'SELECT * FROM Users';
  // Saves the result of the SQL code to a variable
  $result = $con->query($sql);
  // Disconnects from the database
  $con->close();

  // Iterates through the table records and displays them on the web page's table
  while($record = $result -> fetch_array(MYSQLI_NUM)) {
    // Subheading
    echo "<h3>Luke Holten</h3></div>";
    // Main section
    echo "<section>";
    echo "<h5>Details</h5>";
    echo "<ul>";
    echo "<li>Email: luke.holten@doyrms.com</li>";
    echo "<li>Gender: Male</li>";
    echo "<li>Room: 67</li>";
    echo "</ul>";
    echo "<divider></divider>";
    echo "<h5>Status</h5>";
    echo "</section>";
    // Spacer
    echo "<spacer></spacer>";
    // Aside
    echo "<aside>";
    echo "<h3>Events</h3>";
    echo "</aside>";
    break;
  }
}
?>
