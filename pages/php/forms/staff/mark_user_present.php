<?php
// This file contains the code that is executed when a request is sent to the server to mark a user as present
// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');
require '../standard_functions.php';

// Functions
// Used to unrestrict a user
function markUserPresent($userID) {
  global $ini;

  // Query used to mark the user as present (remove them from the 'away' table)
  $query = "DELETE FROM AwayUsers WHERE UserID=?";
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // Prepares and executes the insert statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("i", $userID);
  $stmt->execute();
  // Disconnects from the database
  $con->close();

  // Returns whether the INSERT was successful
  return true;
}

// Variables
// The userID
$userID = fetchUserID($_POST['name_field']);

// Main
// If the user is marked down as away, mark them down as present
if (userAway($userID)) {
  if (markUserPresent($userID)) {
    echo json_encode('The user has been marked down as present successfully');
    exit();
  }
}
else {
  customError(409, 'This user is not marked down as being away, they are present');
  exit();
}
?>
