<?php
// This file contains the code that is executed when a user sign in request is submitted to the server
// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');
require '../standard_functions.php';

// Functions
// Used if the sign in was executed by a member of staff, not a user (self signin)
// This is so the sign in parameters are formatted using the information set by the staff user
// The error messages are also designed with staff users in mind
function executeStaffSignIn($setFields) {

  // Sign in nature declaration
  $staffAction = 1;
  // Do not update the user was last active (since it is a staff action, not a user action)
  $updateLastActive = false;

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
  if (signInUser($userID, $eventID, $minutesLate, $staffAction, $staffMessage, $updateLastActive)) {
    echo json_encode('The user has been signed in successfully');
    exit();
  }

};
// Used if the sign in was intiated by a user, not a member of staff
// This is so the sign in parameters are formatted using the information set by the staff user
// The error messages are also designed with staff users in mind
function executeUserSignIn($setFields) {

  // Sign in nature declaration
  $staffAction = 0;
  // Update when the user was last active (since it is not staff action)
  $updateLastActive = true;

  // UserID declaration
  // If the name field has been set, the UserID is saved to a variable
  if ($setFields['name_field']) {
    $userID = fetchUserID($_POST['name_field']);
  }
  // Returns an error if the name field has not been set
  else {
    customError(409, 'You must enter a username to continue');
    exit();
  }

  // EventID and MinutesLate declaration
  // Uses the current time and LocationID=NULL (because the user is signing in) to determine whether the user is signing in for an event
  $eventDetails = fetchCurrentEvents(NULL, 'in');
  // If they are not signing in for an event, these values are NULL
  $eventID = $eventDetails[0];
  $minutesLate = $eventDetails[1];

  // Checks whether the user is already signed in
  // If there are no events, the user is not allowed to sign in
  if (userSignedIn($userID) && is_null($eventID)) {
    customError(409, 'It seems like you are already signed in');
    exit();
  }

  // There is no staff message
  $staffMessage = NULL;

  // Finally, sign the user in using the previously declared variables
  // For every event the user is attending, sign the user in for that event
  foreach ($events as $event) {signInUser($userID, $eventID, $minutesLate, $staffAction, $staffMessage, $updateLastActive); }
  echo json_encode('You have been signed in successfully!');
  exit();
  

};
// Used to sign the user in
function signInUser($userID, $eventID, $minutesLate, $staffAction, $staffMessage, $updateLastActive) {
  global $ini;

  // Associative array containing the required SQL queries
  $queries = array(
    // Inserts a 'sign in log' into the logs table
    "insert" => "INSERT INTO Log ( UserID, LocationID, LogTime, EventID, MinutesLate, Auto, StaffAction, StaffMessage ) VALUES (?, NULL, CURRENT_TIMESTAMP, ?, ?, 0, ?, ?)",
    // Updates the users curent location
    "update" => "UPDATE Users SET LocationID=NULL WHERE UserID=?"
  );
  // Changes the update query depending on whether the time last active column needs updating
  if ($updateLastActive) {
    $queries["update"] = "UPDATE Users SET LocationID=NULL, LastActive=CURRENT_TIMESTAMP WHERE UserID=?";
  }
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
// Associative array that stores which fields have been set (false by default)
$setFields = [
  'name_field' => false,
  'event_field' => false,
  'event_timing' => false,
  'message_field' => false
];
// For each field, determine if it has been set and set the arrays corresponding value to true
foreach($setFields as $key => $field) { if (verify($_POST[$key])) { $setFields[$key] = true; }} 

// Determines the nature of the sign in and runs the appropriate sign in function
// If a staff user is signing in the user
if ($_POST['staff_action'] == 1) { executeStaffSignIn($setFields); }
// If the user is signing themselves in
else { executeUserSignIn($setFields); }

?>
