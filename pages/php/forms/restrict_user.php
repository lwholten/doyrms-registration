<?php
// This file contains the code that is executed when a request is sent to the server to restrict a user
// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');
require 'standard_functions.php';

// Functions
// Used to restrict a user
function restrictUser($userID, $dateTimeUnrestricted, $reason) {
  global $ini;

  // Query used to insert the user
  $query = "INSERT INTO `RestrictedUsers` (`RestrictedLogID`, `UserID`, `DateTimeRestricted`, `DateTimeUnrestricted`, `Reason`) VALUES (NULL, ?, CURRENT_TIMESTAMP, ?, ?)";
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // Prepares and executes the insert statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("iss", $userID, $dateTimeUnrestricted, $reason);
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
// If the user is not restricted, restrict them
if (!userRestricted($userID)) {
  if (restrictUser($userID, $dateTime, $reason)) {
    echo json_encode('The user has been restricted successfully');
    exit();
  }
}
else {
  customError(409, 'This user is already restricted');
  exit();
}
?>
