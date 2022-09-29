<!DOCTYPE html>
<html lang="en">

<?php
include("php/head.php");
include("css/styles.css");
include("js/scripts.php");
include("php/body.php");

// Global functions used by all the php files contained in /php

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

// Used for testing purposes to echo the array returned by a form POST
function returnPostArray() {
    echo '<pre>'.print_r($_POST, true).'</pre>';
}
?>

</html>
