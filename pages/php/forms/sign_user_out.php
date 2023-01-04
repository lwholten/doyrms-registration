<?php
// This file contains the code that is executed when a user sign out request is submitted to the server
// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');
require 'standard_functions.php';

// Functions
// Used if the sign out was executed by a member of staff, not a user (self signout)
// This is so the sign out parameters are formatted using the information set by the staff user
// The error messages are also designed with staff users in mind
function executeStaffSignOut($setFields) {

  // Sign out nature declaration
  $staffAction = 1;
  // Do not update the user was last active (since it is a staff action, not a user action)
  $updateLastActive = false;

  // UserID declaration
  // If the name field has been set, the UserID is saved to a variable
  if ($setFields['name_field']) {
    $userID = fetchUserID($_POST['name_field'], 'staff');
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
    $locationID = fetchLocationID($_POST['location_field'], 'staff');
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
    $locationID = fetchLocationID($_POST['location_field'], 'staff');
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
    if (signOutUser($userID, $locationID, $eventID, $minutesLate, $staffAction, $staffMessage, $lastActive)) {
      echo json_encode('The user has been signed out successfully');
      exit();
    }
  }
  // If the user is marked down as away, return an error
  else {
    customError(409, 'This user cannot be signed out because they are marked as being away');
    exit();
  }

};
// Used if the sign out was intiated by a user, not a member of staff
// This is so the sign out parameters are formatted using the information set by the staff user
// The error messages are also designed with staff users in mind
function executeUserSignOut($setFields) {

  // Sign out nature declaration
  $staffAction = 0;
  // Update the time that the user was last active (as they are signing themselves out)
  $updateLastActive = true;

  // Return an error if both the user and location fields have not been set
  if (!$setFields['name_field'] && !$setFields['location_field']) {
    customError(409, 'You must enter a username and location to continue');
    exit();
  }

  // UserID declaration
  // If the name field has been set, the UserID is saved to a variable
  if ($setFields['name_field']) {
    $userID = fetchUserID($_POST['name_field'], 'user');
  }
  // Returns an error if the name field has not been set
  else {
    customError(409, 'You must enter a username to continue');
    exit();
  }

  // LocationID declaration
  // If the name field has been set, the UserID is saved to a variable
  if ($setFields['location_field']) {
    $locationID = fetchLocationID($_POST['location_field'], 'user');
  }
  // Returns an error if the name field has not been set
  else {
    customError(409, 'You must enter a location to continue');
    exit();
  }

  // EventID and MinutesLate declaration
  // Uses the locationID and current time to determine whether the user is signing out for an event
  $eventDetails = fetchCurrentEventID($locationID, 'out');
  // If they are not signing out for an event, these values are NULL
  $eventID = $eventDetails[0];
  $minutesLate = $eventDetails[1];

  // There is no staff message
  $staffMessage = NULL;

  // Returns an error if the user is restricted and there is no event
  if (userRestricted($userID) && is_null($eventID)) {
    customError(409, 'It looks like you are restricted, this means that you can only sign out for events');
    exit();
  }

  // If the user is not away, sign them out
  if (!userAway($userID)) {
    // Finally, sign the user out using the previously declared variables
    if (signOutUser($userID, $locationID, $eventID, $minutesLate, $staffAction, $staffMessage, $updateLastActive)) {
      echo json_encode('The user has been signed out successfully');
      exit();
    }
  }
  // If the user is marked down as away, return an error
  else {
    customError(409, 'It looks like you have been marked down as being away, speak to a staff user to mark you back.');
    exit();
  }

}
// Used to sign the user out
function signOutUser($userID, $locationID, $eventID, $minutesLate, $staffAction, $staffMessage, $updateLastActive) {
  global $ini;

  // Associative array containing the required SQL queries
  $queries = array(
    // Inserts a 'sign out log' into the logs table
    "insert" => "INSERT INTO Log ( UserID, LocationID, LogTime, EventID, MinutesLate, Auto, StaffAction, StaffMessage ) VALUES (?, ?, CURRENT_TIMESTAMP, ?, ?, 0, ?, ?)",
    // Updates the users curent location
    "update" => "UPDATE Users SET LocationID=? WHERE UserID=?"
  );
  // Changes the update query depending on whether the time last active column needs updating
  if ($updateLastActive) {
    $queries["update"] = "UPDATE Users SET LocationID=?, LastActive=CURRENT_TIMESTAMP WHERE UserID=?";
  }
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
// Associative array that stores which fields have been set (false by default)
$setFields = [
  'name_field' => false,
  'location_field' => false,
  'event_field' => false,
  'event_timing' => false,
  'message_field' => false
];
// For each field, determine if it has been set and set its corresponding value in the array to True
foreach($setFields as $key => $field) { if (verify($_POST[$key])) { $setFields[$key] = true; } } 

// Determines the nature of the sign out and runs the appropriate sign out function
// If a staff user is signing out the user
if ($_POST['staff_action'] == 1) { executeStaffSignOut($setFields); }
// If the user is signing themselves out
else { executeUserSignOut($setFields); }

?>
