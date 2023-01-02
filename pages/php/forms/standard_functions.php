<?php
// This file contains standard functions used across the various forms
// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');

// Functions
// Used to return a custom error to the page
function customError($errorCode, $errorMsg) {
  header("HTTP/1.0 $errorCode $errorMsg");
  exit();
}
// Used to check whether a variable has been set and does not contain only whitespace or spaces
// This is useful for optional inputs as this function can verify if the variable has been set
function verify($var) {
  // Removes all spaces and whitespace (blank characters)
  $var = rtrim($var);
  // Checks whether the variable is NULL, Empty or set
  if (!is_null($var) && isset($var) && !empty($var)) {
    return true;
  }
  else {
    return false;
  }
}
// Used to fetch the UserID associated with a user
function fetchUserID($name) {
  global $ini;

  // Splits the name input into first and last names
  $fname = explode(" ", $name)[0];
  $lname = explode(" ", $name)[1];
  // Used to check whether a the user is already signed in
  $query = "SELECT UserID FROM Users WHERE Users.Forename=? AND Users.Surname=? LIMIT 1";
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // Prepares and executes the statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("ss", $fname, $lname);
  $stmt->execute();
  // Binds the result to a variable
  $stmt->bind_result($result);
  $stmt->fetch();
  // Disconnects from the database
  $con->close();

  if (is_null($result)) {
    customError(422, 'The users name provided is invalid and that user cannot be found');
    exit();
  }
  else {
    return $result;
  }
}
// Used to fetch the EventID associated with the event
function fetchEventID($event, $nature) {
  global $ini;

  // Assigns the most appropriate query depending on the events nature (sign in or sign out)
  if ($nature === 'in') {
    // Sign in events
    $query = "SELECT EventID FROM Events WHERE Events.Event=? AND SignInEvent=1 LIMIT 1";
    $signInEvent = 1;
  }
  elseif ($nature === 'out') {
    // Sign out events
    $query = "SELECT EventID FROM Events WHERE Events.Event=? AND SignInEvent=0 LIMIT 1";
    $signInEvent = 0;
  }
  else {
    // Both sign in and sign out events
    $query = "SELECT EventID FROM Events WHERE Events.Event=? LIMIT 1";
    $signInEvent = NULL;
  }

  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // Prepares and executes the statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("s", $event);
  $stmt->execute();
  // Binds the result to a variable
  $stmt->bind_result($result);
  $stmt->fetch();
  // Disconnects from the database
  $con->close();

  if (is_null($result)) {
    customError(422, 'The event provided is invalid and could not be found');
    exit();
  }
  else {
    return $result;
  }
}
// Used to fetch the EventID associated with the event
function fetchLocationID($location) {
  global $ini;

  // Used to check whether a the user is already signed in
  $query = "SELECT LocationID FROM Locations WHERE Locations.LocationName=? LIMIT 1";
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // Prepares and executes the statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("s", $location);
  $stmt->execute();
  // Binds the result to a variable
  $stmt->bind_result($result);
  $stmt->fetch();
  // Disconnects from the database
  $con->close();

  if (is_null($result)) {
    customError(422, 'The location provided is invalid and could not be found');
    exit();
  }
  else {
    return $result;
  }
}
// Used to check whether a user has already attended an event
function userAttendedEvent($userID, $eventID) {
  global $ini;

  // Used to check whether a the user has attended an event (1 if true, 0 if false)
  $query = "SELECT (CASE WHEN EXISTS (SELECT * FROM Log WHERE Log.UserID=? AND Log.EventID=? AND CAST(Log.LogTime AS Date)=CURRENT_DATE) THEN 1 ELSE 0 END) AS Attended";
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // Prepares and executes the statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("ii", $userID, $eventID);
  $stmt->execute();
  // Binds the result to a variable
  $stmt->bind_result($result);
  $stmt->fetch();
  // Disconnects from the database
  $con->close();

  // Returns false if the user did not attend
  if ($result === 0 || $result === "0") {
    return false;
  }
  // Returns true if the user did attend
  elseif ($result === 1 || $result === "1") {
    return true;
  }
  else {
    customError(500, 'A server error has occured while checking if the user has attended the event');
    exit();
  }
}
// Used to check whether a user is signed in
function userSignedIn($userID) {
  global $ini;

  // Used to check whether a the user is already signed in (1 if signed in, 0 if signed out)
  $query = "SELECT (CASE WHEN EXISTS (SELECT * FROM Users WHERE Users.UserID=? AND Users.LocationID IS NULL) THEN 1 ELSE 0 END) AS SignedIn";
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // Prepares and executes the statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("i", $userID);
  $stmt->execute();
  // Binds the result to a variable
  $stmt->bind_result($result);
  $stmt->fetch();
  // Disconnects from the database
  $con->close();

  // Returns false if the user is signed out
  if ($result === 0 || $result === "0") {
    return false;
  }
  // Returns true if the user is signed in
  elseif ($result === 1 || $result === "1") {
    return true;
  }
  else {
    customError(500, 'A server error has occured while checking whether the user is signed in or out');
    exit();
  }
}
// Used to check whether a user is restricted
function userRestricted($userID) {
  global $ini;

  // Used to check whether a the user is restricted (1) or not (0)
  $query = "SELECT (CASE WHEN EXISTS (SELECT * FROM RestrictedUsers WHERE RestrictedUsers.UserID=?) THEN 1 ELSE 0 END) AS Restricted";
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // Prepares and executes the statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("i", $userID);
  $stmt->execute();
  // Binds the result to a variable
  $stmt->bind_result($result);
  $stmt->fetch();
  // Disconnects from the database
  $con->close();

  // Returns false if the user is not restricted
  if ($result === 0 || $result === "0") {
    return false;
  }
  // Returns true if the user is restricted
  elseif ($result === 1 || $result === "1") {
    return true;
  }
  else {
    customError(500, 'A server error has occured while checking whether the user is restricted');
    exit();
  }
}
// Used to check whether a user is away
function userAway($userID) {
  global $ini;

  // Used to check whether a the user is away (1) or not (0)
  $query = "SELECT (CASE WHEN EXISTS (SELECT * FROM AwayUsers WHERE AwayUsers.UserID=?) THEN 1 ELSE 0 END) AS Away";
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // Prepares and executes the statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("i", $userID);
  $stmt->execute();
  // Binds the result to a variable
  $stmt->bind_result($result);
  $stmt->fetch();
  // Disconnects from the database
  $con->close();

  // Returns false if the user is not away
  if ($result === 0 || $result === "0") {
    return false;
  }
  // Returns true if the user is away
  elseif ($result === 1 || $result === "1") {
    return true;
  }
  else {
    customError(500, 'A server error has occured while checking whether the user is restricted');
    exit();
  }
}
?>
