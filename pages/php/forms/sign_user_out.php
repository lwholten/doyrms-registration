<?php
// This file contains the code that is executed when a user sign out request is submitted to the server
// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');
require 'standard_functions.php';

// Functions
// Used to sign the user out
function signOutUser($userID, $locationID, $eventID, $minutesLate, $staffAction, $staffMessage) {
  global $ini;

  // Associative array containing the required SQL queries
  $queries = array(
    // Inserts a 'sign out log' into the logs table
    "insert" => "INSERT INTO Log ( UserID, LocationID, LogTime, EventID, MinutesLate, Auto, StaffAction, StaffMessage ) VALUES (?, ?, CURRENT_TIMESTAMP, ?, ?, 0, ?, ?)",
    // Updates the users curent location
    "update" => "UPDATE Users SET LocationID=? WHERE UserID=?"
  );
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // Prepares and executes the insert statement
  $stmt = $con->prepare($queries['insert']);
  $stmt->bind_param("iiiiis", $userID, $locationID, $eventID, $minutesLate, $staffAction, $staffMessage);
  $stmt->execute();
  // Prepares and executes the update statement
  $stmt = $con->prepare($queries['update']);
  $stmt->bind_param("ii", $locationID, $userID);
  $stmt->execute();
  // Disconnects from the database
  $con->close();

  // Returns whether the queries were successful
  return true;
}

// Main

// Declares the nature of the sign out
// If a staff user is signing out the user, set staffAction variable to 1 (true)
if ($_POST['staff_action'] == 1) {
  $staffAction = 1;
}
else {
  $staffAction = 0;
}

// Associatign array that stores which fields have been set (false by default)
$setFields = [
  'name_field' => false,
  'location_field' => false,
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
  customError(409, 'You must input a name to sign a user out');
  exit();
}

// EventID and LocationID declaration
// If the locations field and events field have not been set
if (!$setFields['location_field'] && !$setFields['event_field']) {

  // Raises an error
  customError(409, 'You must select a location, event or both to sign out a user');
  exit();

}
// If only the locations field has been set
else if ($setFields['location_field'] && !$setFields['event_field']) {
  
  // Set the locationID to the entered locations ID
  $locationID = fetchLocationID($_POST['location_field']);
  // Set the eventID to NULL as no event was set
  $eventID = NULL;

  // Raises an error if the user is restricted, as they may only sign out for an event
  if (userRestricted($userID)) {
    customError(409, 'This user is restricted and may only be signed out for an event');
    exit();
  }

  // Raises an error if the user is already signed out
  if (!userSignedIn($userID)) {
    customError(409, 'This user is already signed out');
    exit();
  }

}
// If only the events field has been set
else if (!$setFields['location_field'] && $setFields['event_field']) {

  // Sets the locationID to the users current location
  $locationID = fetchCurrentLocationID($userID);
  // Sets the eventID to the entered events ID
  $eventID = fetchEventID($_POST['event_field'], 'out');

  // Raises an error if the user has already attended the event
  if (userAttendedEvent($userID, $eventID)) {
    customError(409, 'This user has already signed out for this event');
    exit();
  }
  
}
// If both fields have been set
else if ($setFields['location_field'] && $setFields['event_field']) {

  // Sets the locationID to the entered locations ID
  $locationID = fetchLocationID($_POST['location_field']);
  // Sets the eventID to the entered events ID
  $eventID = fetchEventID($_POST['event_field'], 'out');

  // Raises an error if the event is not set for the location
  if (!eventMatchesLocation($eventID, $locationID)) {
    customError(409, 'The location you selected must match the event');
    exit();      
  }
  // Raises an error if the user has already attended the event
  else if (userAttendedEvent($userID, $eventID)) {
    customError(409, 'This user has already signed out for this event');
    exit();
  }
  // Raises an error if the user is already signed out
  else if (!userSignedIn($userID)) {
    customError(409, 'This user is already signed out');
    exit();
  }

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

// If the user is not away, sign them out
if (!userAway($userID)) {
  // Finally, sign the user out using the previously declared variables
  if (signOutUser($userID, $locationID, $eventID, $minutesLate, $staffAction, $staffMessage)) {
    echo json_encode('The user has been signed out successfully');
    exit();
  }
}
// If the user is marked down as away, return an error
else {
  customError(409, 'This user cannot be signed out because they are marked as being away');
  exit();
}
?>
