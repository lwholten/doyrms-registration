<!DOCTYPE php>
<?php
// Used to connect to the database
function databaseConnect() {
  // Connection details
  $servername = "localhost";
  $username = "dreg_user";
  $password = "epq";

  // Create connection
  $conn = new mysqli($servername, $username, $password);

  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  echo "Connected successfully";
}

// Used to terminate the connection to the database
function databaseDisconnect() {
  $conn->close();
}
?>
