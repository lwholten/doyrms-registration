<?php
// This file contains the code that is executed when a user sign in request is submitted to the server
// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');
require 'standard_functions.php';

// Functions
// Used to sign the user in
function signInUser($userID, $eventID, $staffAction, $staffMessage) {
  global $ini;

  // Associative array containing the required SQL queries
  $queries = array(
    // Inserts a 'sign in log' into the logs table
    "insert" => "INSERT INTO Log ( UserID, LocationID, LogTime, EventID, MinutesLate, Auto, StaffAction, StaffMessage ) VALUES (?, NULL, CURRENT_TIMESTAMP, ?, 0, 0, ?, ?)",
    // Updates the users curent location
    "update" => "UPDATE Users SET LastActive=CURRENT_TIMESTAMP, LocationID=NULL WHERE UserID=?"
  );
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // Prepares and executes the insert statement
  $stmt = $con->prepare($queries['insert']);
  $stmt->bind_param("iiis", $userID, $eventID, $staffAction, $staffMessage);
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

// The EventID
// If the event field has data, fetch the eventID of the data, otherwise NULL
if (verify($_POST['event_field'])) {
  $eventID = fetchEventID($_POST['event_field'], 'in');

  // If the user has already attended the event, return an error
  if (userAttendedEvent($userID, $eventID)) {
    customError(409, 'This user has already signed in for this event');
    exit();
  }
  else {
    // This tells us that the user has not signed off for this event
    $signedOffForEvent= false;
  }

}
else {
  $eventID = NULL;
  $signedOffForEvent = true;
}

// The nature of the sign in
// If a staff user is signing in the user, set staffAction variable to 1 (true)
if ($_POST['staff_action'] == 1) {
  $staffAction = 1;
}
else {
  $staffAction = 0;
}

// The message
// If the message field has data, set the message to that data, otherwise NULL
if (verify($_POST['message_field'])) {
  $staffMessage = $_POST['message_field'];
}
else {
  $staffMessage = NULL;
}

// Main
// If the user is signed in and signed off for the event
if (userSignedIn($userID) && $signedOffForEvent) {
  customError(409, 'This user is already signed in');
  exit();
}
// If the user is signed out or has not signed off for the event
// Note that this will trigger if the user is signed in but has not signed off for the event
else {
  // Sign the user in
  if (signInUser($userID, $eventID, $staffAction, $staffMessage)) {
    echo json_encode('The user has been signed in successfully');
    exit();
  }
}
?>
