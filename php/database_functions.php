<!DOCTYPE php>
<?php
// This file contains functions that interact with the database stored on the server


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

// Used to verify a staff members login details
function verifyStaffLoginDetails($username, $password) {
  // Temporary code, replace with SQL queries connecting to the database to
  // actually check if the user is on the system and has entered the incorrect
  // login information
  if ($username === "staff" && $password === "password") {
    return True;
  }
  else {
    return False;
  }
}
?>
