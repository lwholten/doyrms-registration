<?php
// This file contains the code that is executed when a user sign in request is submitted to the server
// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');
require 'standard_functions.php';

// Functions
// Used to sign the user in
function signInUser($userID, $eventID, $minutesLate, $staffAction, $staffMessage) {
  global $ini;

  // Associative array containing the required SQL queries
  $queries = array(
    // Inserts a 'sign in log' into the logs table
    "insert" => "INSERT INTO Log ( UserID, LocationID, LogTime, EventID, MinutesLate, Auto, StaffAction, StaffMessage ) VALUES (?, NULL, CURRENT_TIMESTAMP, ?, ?, 0, ?, ?)",
    // Updates the users curent location
    "update" => "UPDATE Users SET LocationID=NULL WHERE UserID=?"
  );
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // Prepares and executes the insert statement
  $stmt = $con->prepare($queries['insert']);
  $stmt->bind_param("iiiis", $userID, $eventID, $minutesLate, $staffAction, $staffMessage);
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

// Main

// Declares the nature of the sign in
// If a staff user is signing in the user, set staffAction variable to 1 (true)
if ($_POST['staff_action'] == 1) {
  $staffAction = 1;
}
else {
  $staffAction = 0;
}

// Associatign array that stores which fields have been set (false by default)
$setFields = [
  'name_field' => false,
  'event_field' => false,
  'event_timing' => false,
  'message_field' => false
];
// For each field, determine if it has been set and set the arrays corresponding value to true
foreach($setFields as $key => $field) { 

  if (verify($_POST[$key])) { $setFields[$key] = true; }

} 

// UserID declaration
// If the name field has been set, the UserID is saved to a variable
if ($setFields['name_field']) {
  $userID = fetchUserID($_POST['name_field']);
}
// Returns an error if the name field has not been set
else {
  customError(409, 'You must input a name to sign a user in');
  exit();
}

// EventID declaration
// events field has not been set
if (!$setFields['event_field']) {

  // Set the EventID to NULL (No Event)
  $eventID = NULL;

  // Raises an error if the user is already signed in
  if (userSignedIn($userID)) {
    customError(409, 'This user is already signed in');
    exit();
  }

}
// If the events field has been set
else if ($setFields['event_field']) {

  // Sets the eventID to the entered events ID
  $eventID = fetchEventID($_POST['event_field'], 'in');

  // Raises an error if the user has already attended the event
  if (userAttendedEvent($userID, $eventID)) {
    customError(409, 'This user has already signed in for this event');
    exit();
  }

  // Note, if a user is already signed in, but is signed in for an event, they are signed in again
  
}

// Minutes late declaration
// If the event field is set and the event timing
if ($setFields['event_field'] && $setFields['event_timing']) {
  $minutesLate = $_POST['event_timing'];
}
// If the event field is set and the event timing is not set, set the minutes late to 0
else if ($setFields['event_field'] && !$setFields['event_timing']) {
  $minutesLate = 0;
}
// Any other scenario, set the minutes late to NULL
else {
  $minutesLate = NULL;
}

// Message declaration
// If the message field has been set, save the message
if ($setFields['message_field']) {
  $staffMessage = $_POST['message_field'];
}
// Otherwise, set the message to NULL
else {
  $staffMessage = NULL;
}

// Finally, sign the user in using the previously declared variables
if (signInUser($userID, $eventID, $minutesLate, $staffAction, $staffMessage)) {
  echo json_encode('The user has been signed in successfully');
  exit();
}

?>
