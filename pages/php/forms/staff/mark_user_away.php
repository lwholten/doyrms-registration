<?php
// This file contains the code that is executed when a request is sent to the server to mark a user as away
// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');
require '../standard_functions.php';

// Functions
// Used to restrict a user
function markUserAway($userID, $dateTimeReturn, $reason) {
  global $ini;

  $queries = array(
    // Inserts the user into the 'away' table
    "insert" => "INSERT INTO `AwayUsers` (`AwayLogID`, `UserID`, `DateTimeAway`, `DateTimeReturn`, `Reason`) VALUES (NULL, ?, CURRENT_TIMESTAMP, ?, ?)",
    // Sets the users location to NULL (signing them in without performing a full sign in procedure)
    "update" => "UPDATE Users SET LastActive=CURRENT_TIMESTAMP, LocationID=NULL WHERE UserID=?"
  );
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // Prepares and executes the insert statement
  $stmt = $con->prepare($queries['insert']);
  $stmt->bind_param("iss", $userID, $dateTimeReturn, $reason);
  $stmt->execute();
  // Prepares and executes the update statement
  $stmt = $con->prepare($queries['update']);
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

// The reason
// If the reason field has data, set the reason to that data, otherwise NULL
if (verify($_POST['message_field'])) {
  $reason = $_POST['message_field'];
}
else {
  $reason = NULL;
}

// The time
if (verify($_POST['time_field'])) {
  $time = $_POST['time_field'];
}
else {
  $time = NULL;
}

// The date
// If the date has been input, set the date to the input date
if (verify($_POST['date_field'])) {
  $date = $_POST['date_field'];
}
// If the date has been left empty but the time was specified, use todays date
elseif (!is_null($time)) {
  $date = date("Y-m-d");
}
// If neither time nor date are specified, set date to NULL
else {
  $date = NULL;
}

// Combines the date and time into a datetime variable
// If both values are NULL, so is the datetime
if (is_null($time) && is_null($date)) {
  $dateTime = NULL;
}
// If only the time is null, set the time to midnight
elseif (is_null($time) && !is_null($date)) {
  $dateTime = date('Y-m-d H:i:s', strtotime("$date midnight"));
}
// Otherwise, set the date time to the date and time set by the user
else {
  $dateTime = date('Y-m-d H:i:s', strtotime("$date $time"));
}

// Main
// If the user is already away, report an error and down mark this user as away
if (!userAway($userID)) {
  if (markUserAway($userID, $dateTime, $reason)) {
    echo json_encode('The user has been marked as away successfully');
    exit();
  }
}
else {
  customError(409, 'This user is already marked down as being away');
  exit();
}
?>
