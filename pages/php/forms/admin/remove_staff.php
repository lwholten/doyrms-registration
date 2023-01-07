<?php
// This file contains the code that is executed when a request is sent to the server to remove a staff user
// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');
require '../standard_functions.php';

// Functions
// Used to unrestrict a user
function deleteStaffUser($username) {
  global $ini;

  // Query used to unrestrict the user
  $query = "DELETE FROM Staff WHERE Username=?";
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // Prepares and executes the insert statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("s", $username);
  $stmt->execute();
  // Disconnects from the database
  $con->close();

  // Returns whether the INSERT was successful
  return true;
}

// Variables
// The staff username
$username = $_POST['username_field'];

// Main
// If the staff user exists, delete their account
if (staffUserExists($username)) {
  if (deleteStaffUser($username)) {
    echo json_encode('The staff user has been deleted successfully');
    exit();
  }
}
else {
  customError(409, 'This staff user does not exist');
  exit();
}
?>
