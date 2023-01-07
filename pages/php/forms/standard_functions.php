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
function fetchUserID($name, $errorType='staff') {
  global $ini;

  // Associative array containing the error messages for this function
  $errorTypes = [
    "staff" => "The users name provided is invalid and that user cannot be found",
    "user" => "Please make sure the name you entered is valid"
  ];

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

  if (is_null($result) && $errorType != 'none') {
    customError(422, $errorTypes[$errorType]);
    exit();
  }
  else {
    return $result;
  }
}
// Used to fetch the EventID associated with the event
function fetchEventID($event, $nature, $errorType='staff') {
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
    customError(422, 'The event provided could not be found');
    exit();
  }
  else {
    return $result;
  }
}
// Used to fetch the EventID associated with the event
function fetchLocationID($location, $errorType='staff') {
  global $ini;

  // Associative array containing the error messages for this function
  $errorTypes = [
    "staff" => "The location provided could not be found",
    "user" => "Please make sure the location you entered is valid"
  ];

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

  if (is_null($result) && $errorType != 'none') {
    customError(422, $errorTypes[$errorType]);
    exit();
  }
  else {
    return $result;
  }
}
// Used to fetch a users current location ID
function fetchCurrentLocationID($userID) {
  global $ini;

  // Used to check whether a the user is already signed in
  $query = "SELECT LocationID FROM Users WHERE Users.UserID=? LIMIT 1";
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // Prepares and executes the statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("s", $userID);
  $stmt->execute();
  // Binds the result to a variable
  $stmt->bind_result($result);
  $stmt->fetch();
  // Disconnects from the database
  $con->close();

  return $result;
}
// Used to fetch the current times event ID
function fetchCurrentEvents($locationID, $eventNature) {
  global $ini;
  // This function uses a locationID to determine a current event at the time of executing
  // If there is an event at this time, it will return the EventID and how late/early the current time is realtive to the event
  // These values are returned as an array in the format: [eventID, minutesLate]
  
  // NOTES:
  // $eventNature -> String: Must determine the nature of the event, whether signing in 'in' or signing out 'out'
  // $locationID -> Integer: The ID of the location where the event may be taking place

  // SQL
  // Determines an appropraite query depending on the events nature
  if ($eventNature == 'in') {
    $query = "SELECT EventID, StartTime, EndTime, Deviation FROM Events WHERE SignInEvent=1 AND LocationID=? AND Days LIKE CONCAT('%', ?, '%') ORDER BY StartTime ASC";
  }
  elseif ($eventNature == 'out') {
    $query = "SELECT EventID, StartTime, EndTime, Deviation FROM Events WHERE SignInEvent=0 AND LocationID=? AND Days LIKE CONCAT('%', ?, '%') ORDER BY StartTime ASC";
  }
  else {
    $query = "SELECT EventID, StartTime, EndTime, Deviation FROM Events WHERE LocationID=? AND Days LIKE CONCAT('%', ?, '%') ORDER BY StartTime ASC LIMIT 1";
  }

  // Gets the current day and saves it in string format
  $day = "SMTWRFU"[date("N", strtotime(date('l')))];

  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);

  // An array to store each event the user may be attending (since there may be more than one)
  // It stores these in a two-dimensional array, with [eventID, minutesLate] being pushed into this one
  $events = [];

  // Prepares and executes the statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("is", $locationID, $day);
  $stmt->execute();

  // Fetches the results
  $result = $stmt->get_result();
  // Iterates through every result, then appends each event the user has attended to a two-dimensional array
  while ($row = $result->fetch_array(MYSQLI_NUM)) {

    // If the deviation is NULL or empty, set the deviation to 0 minutes
    if (is_null($row[3]) || empty($row[3])) { $deviation = 0; }
    else { $deviation = $row[3]; }

    // Gets the current time
    $currentTime = date('H:i:s', time());

    // Calculates the early and late times by adding and subtracting the deviation time respectively
    $earlyTime = date('H:i:s', strtotime($row[1]) - (60*$deviation));
    $lateTime = date('H:i:s', strtotime($row[2]) + (60*$deviation));

    // Sets the start and end times for the event using the data fetched from the table
    $startTime = $row[1];
    $endTime = $row[2];

    // If the user is early for the event
    if ($currentTime >= $earlyTime && $currentTime <= $startTime) {
      // Calculates how early the user is (early values are stored as negative integers)
      $minutesLate = -1*intval(date('i', (strtotime($startTime) - strtotime($currentTime))));
      // Saves the users data for this event to an array and pushes it to the list of attended events
      array_push($events, [$row[0], $minutesLate]);
      continue;
    }
    // If the user is on time for the event and breaks out of the while loop
    else if ($currentTime >= $startTime && $currentTime <= $endTime) {
      // The user is on time, so the minutesLate variable is set to 0
      $minutesLate = 0;
      // Saves the users data for this event to an array and pushes it to the list of attended events
      array_push($events, [$row[0], $minutesLate]);
      continue;
    }
    // If the user is late for the event
    else if ($currentTime >= $endTime && $currentTime <= $lateTime) {
      // Calculates how late the user is (late values are stored as a positive integer)
      $minutesLate = intval(date('i', (strtotime($currentTime) - strtotime($endTime))));
      // Saves the users data for this event to an array and pushes it to the list of attended events
      array_push($events, [$row[0], $minutesLate]);
      continue;
    }

  }
  // Disconnects from the database
  $con->close();

  // Returns an array containing all events the user has attended with this signout,
  // The array is two-dimensionsl containing each 'event' in the format [eventID, minutesLate]
  return $events;
}
function generateRandomSalt() {
  global $ini;
  return bin2hex(random_bytes($ini['password_salt_length']));
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
// Used to chech whether a user exists
function userExists($fname, $lname) {
  global $ini;

  // Used to check whether a the user is away (1) or not (0)
  $query = "SELECT (CASE WHEN EXISTS (SELECT * FROM Users WHERE Users.Forename=? AND Users.Surname=?) THEN 1 ELSE 0 END) AS userExists";
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

  // Returns false if the staff user exists
  if ($result === 0 || $result === "0") {
    return false;
  }
  // Returns true if the staff user does not exist
  elseif ($result === 1 || $result === "1") {
    return true;
  }
  else {
    customError(500, 'A server error has occured while checking whether the user already exists');
    exit();
  }
} 
// Used to check whether a staff user exists
function staffUserExists($username) {
  global $ini;

  // Used to check whether a the user is away (1) or not (0)
  $query = "SELECT (CASE WHEN EXISTS (SELECT * FROM Staff WHERE Staff.Username=?) THEN 1 ELSE 0 END) AS staffUserExists";
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // Prepares and executes the statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("s", $username);
  $stmt->execute();
  // Binds the result to a variable
  $stmt->bind_result($result);
  $stmt->fetch();
  // Disconnects from the database
  $con->close();

  // Returns false if the staff user exists
  if ($result === 0 || $result === "0") {
    return false;
  }
  // Returns true if the staff user does not exist
  elseif ($result === 1 || $result === "1") {
    return true;
  }
  else {
    customError(500, 'A server error has occured while checking whether the staff user already exists');
    exit();
  }
}
// Used to check whether a location exists
function locationExists($location) {
  global $ini;

  // Used to check whether a the user is away (1) or not (0)
  $query = "SELECT (CASE WHEN EXISTS (SELECT * FROM Locations WHERE Locations.LocationName=?) THEN 1 ELSE 0 END) AS locationExists";
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

  // Returns false if the location exists
  if ($result === 0 || $result === "0") {
    return false;
  }
  // Returns true if the location does not exist
  elseif ($result === 1 || $result === "1") {
    return true;
  }
  else {
    customError(500, 'A server error has occured while checking whether the location already exists');
    exit();
  }
}
// Used to check whether an event exists
function eventExists($event) {
  global $ini;

  // Used to check whether a the user is away (1) or not (0)
  $query = "SELECT (CASE WHEN EXISTS (SELECT * FROM Events WHERE Events.Event=?) THEN 1 ELSE 0 END) AS eventExists";
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

  // Returns false if the event exists
  if ($result === 0 || $result === "0") {
    return false;
  }
  // Returns true if the event does not exist
  elseif ($result === 1 || $result === "1") {
    return true;
  }
  else {
    customError(500, 'A server error has occured while checking whether the event already exists');
    exit();
  }
}
// Used to check whether an event matches a location
function eventMatchesLocation($eventID, $locationID) {
  global $ini;

  // Query
  $query = "SELECT (CASE WHEN ? IN (SELECT LocationID FROM Events WHERE EventID=?) THEN 1 ELSE 0 END);";
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // Prepares and executes the statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("ii", $locationID, $eventID);
  $stmt->execute();
  // Binds the result to a variable
  $stmt->bind_result($result);
  $stmt->fetch();
  // Disconnects from the database
  $con->close();

  // Returns false if they do not match
  if ($result === 0 || $result === "0") {
    return false;
  }
  // Returns true if they do match
  elseif ($result === 1 || $result === "1") {
    return true;
  }
  else {
    customError(500, 'A server error has occured while checking whether the location and event match');
    exit();
  }
}
// Checks whether the staff users password is correct using a staff members username or ID (ID is preferable)
function checkStaffPassword($username=NULL, $staffID=NULL, $password) {
  global $ini;
  // Selects the staff users salt and hash from the database
  $query = "SELECT Salt, Hash FROM Staff WHERE Username=? OR staffID=? LIMIT 1";
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // Prepares and executes the statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("si", $username, $staffID);
  $stmt->execute();
  // Binds the result to their respective variables
  $stmt->bind_result($result['salt'], $result['hash']);
  $stmt->fetch();
  // Disconnects from the database
  $con->close();

  // Concatinates the accounts salt to the start of the password
  $saltedPassword = $result['salt'].$password;

  // Verifies the password and returns the result
  if (password_verify($saltedPassword, $result['hash'])) {
      unset($result);
      return true;
  } else {
      unset($result);
      return false;
  }
}
// Updates a staff users password
function updateStaffPassword($staffID, $newPassword) {
  global $ini;

  // Generates a new salt
  $salt = generateRandomSalt();
  // Concatinates the salt to the START of the password
  $saltedPassword = $salt.$newPassword;
  // Hashes the salted password
  $hash = password_hash($saltedPassword, PASSWORD_BCRYPT);

  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // Prepares the UPDATE query
  $stmt = $con->prepare("UPDATE Staff SET Salt=?, Hash=?, LastChangedPassword=CURRENT_TIMESTAMP WHERE StaffID=?");
  $stmt->bind_param("ssi", $salt, $hash, $staffID);
  // Executes the stement
  $stmt->execute();
  // Disconnects from the database
  $con->close();
}
// Checks whether the password meets the password complexity requirements set in 'app.ini'
function meetsPasswordComplexityRequirements($password) {
  global $ini;

  // If the minimum password length is set
  // Compare the password length to the minimum password length and return an error if its too short
  if (!($ini['password_min_length'] === 'none') && (strlen($password) < $ini['password_min_length'])) {
       return [false, 'Passwords must be at least '.$ini['password_min_length'].' character'.(($ini['password_min_length'] > 1) ? 's' : '').' long']; } // The inline IF statement appends an S for plurals

  // If the maximum password length is set
  // Compare the password length to the maxmimum password length and return an error if its too long
  elseif (!($ini['password_max_length'] === 'none') && (strlen($password) > $ini['password_max_length'])) { 
      return [false, 'Passwords can be a maximum of '.$ini['password_max_length'].' character'.(($ini['password_max_length'] > 1) ? 's' : '').' long']; }

  // If the minimum capital letter count is set
  // Compare the number of capital letters in the new password to the minimum allowed and return an error if there is not enough
  elseif (!($ini['password_min_capitals'] === 'none') && ($ini['password_min_capitals'] > preg_match_all('/[A-Z]/', $password))) {
       return [false, 'Passwords must have at least '.$ini['password_min_capitals'].' capital letter'.(($ini['password_min_capitals'] > 1) ? 's' : '')]; }
  
  // If the maximum capital letter count is set
  // Compare the number of capital letters in the new password to the maximum allowed and return an error if there are too many
  elseif (!($ini['password_max_capitals'] === 'none') && ($ini['password_max_capitals'] < preg_match_all('/[A-Z]/', $password))) {
       return [false, 'Passwords may have no more than '.$ini['password_max_capitals'].' capital letter'.(($ini['password_max_capitals'] > 1) ? 's' : '')]; }

  // If the minimum number count is set
  // Compare the number of numbers in the new password to the minimum allowed and return an error if there is not enough
  elseif (!($ini['password_min_numbers'] === 'none') && ($ini['password_min_numbers'] > preg_match_all('/[0-9]/', $password))) { 
      return [false, 'Passwords must have at least '.$ini['password_min_numbers'].' number'.(($ini['password_min_numbers'] > 1) ? 's' : '')]; }
  
  // If the maximum number count is set
  // Compare the number of numbers in the new password to the maximum allowed and return an error if there are too many
  elseif (!($ini['password_max_numbers'] === 'none') && ($ini['password_max_numbers'] < preg_match_all('/[0-9]/', $password))) { 
      return [false, 'Passwords may have no more than '.$ini['password_max_numbers'].' number'.(($ini['password_max_numbers'] > 1) ? 's' : '')]; }


  // If the minimum special character count is set
  // Compare the number of special characters in the new password to the minimum allowed and return an error if there is not enough
  elseif (!($ini['password_min_symbols'] === 'none') && ($ini['password_min_symbols'] > preg_match_all('/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/', $password))) { 
      return [false, 'Passwords must have at least '.$ini['password_min_symbols'].' special character'.(($ini['password_min_symbols'] > 1) ? 's' : '')]; }
  
  // If the maximum special character count is set
  // Compare the number of special characters in the new password to the maximum allowed and return an error if there are too many
  elseif (!($ini['password_max_symbols'] === 'none') && ($ini['password_max_symbols'] < preg_match_all('/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/', $password))) { 
      return [false, 'Passwords may have no more than '.$ini['password_max_symbols'].' special character'.(($ini['password_max_symbols'] > 1) ? 's' : '')]; }

  // If all checks have been passed successfully
  else { return [true, NULL]; }

}
// Checks whether a username meets the username requirements
function meetsUsernameComplexityRequirements($username) {
  global $ini;

  // If the minimum username length is set
  // Compare the username length to the minimum username length and return an error if its too short
  if (!($ini['username_min_length'] === 'none') && (strlen($username) < $ini['username_min_length'])) {
       return [false, 'Usernames must be at least '.$ini['username_min_length'].' character'.(($ini['username_min_length'] > 1) ? 's' : '').' long']; } // The inline IF statement appends an S for plurals

  // If the maximum username length is set
  // Compare the username length to the maxmimum username length and return an error if its too long
  elseif (!($ini['username_max_length'] === 'none') && (strlen($username) > $ini['username_max_length'])) { 
      return [false, 'Usernames can be a maximum of '.$ini['username_max_length'].' character'.(($ini['username_max_length'] > 1) ? 's' : '').' long']; }

  // If the username may not contain capital letters
  // If it contains capital letters, return an error
  elseif (!($ini['username_accepts_capitals'] === 'yes') && (preg_match_all('/[A-Z]/', $username) != 0)) {
       return [false, 'Usernames may not contain capital letters']; }

  // If the username may not contain numbers
  // If it contains numbers, return an error
  elseif (!($ini['username_accepts_numbers'] === 'yes') && (preg_match_all('/[0-9]/', $username) != 0)) {
    return [false, 'Usernames may not contain numbers']; }

  // If the username may not contain symbols
  // If it contains symbols, return an error
  elseif (!($ini['username_accepts_symbols'] === 'yes') && (preg_match_all('/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/', $username) != 0)) {
    return [false, 'Usernames may not contain symbols']; }
  
  // If all checks have been passed successfully
  else { return [true, NULL]; }
}
// Checks whether a name meets the name requirements
function meetsNameComplexityRequirements($name) {
  global $ini;

  // If the minimum name length is set
  // Compare the name length to the minimum name length and return an error if its too short
  if (!($ini['name_min_length'] === 'none') && (strlen($name) < $ini['name_min_length'])) {
       return [false, 'Names must be at least '.$ini['name_min_length'].' character'.(($ini['name_min_length'] > 1) ? 's' : '').' long']; } // The inline IF statement appends an S for plurals

  // If the maximum name length is set
  // Compare the name length to the maxmimum name length and return an error if its too long
  elseif (!($ini['name_max_length'] === 'none') && (strlen($name) > $ini['name_max_length'])) { 
      return [false, 'Names can be a maximum of '.$ini['name_max_length'].' character'.(($ini['name_max_length'] > 1) ? 's' : '').' long']; }

  // If the name may not contain numbers
  // If it contains numbers, return an error
  elseif (!($ini['name_accepts_numbers'] === 'yes') && (preg_match_all('/[0-9]/', $name) != 0)) {
    return [false, 'Names may not contain numbers']; }
  
  // If all checks have been passed successfully
  else { return [true, NULL]; }
}
function containsSymbols($string) {
  if (preg_match_all('/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/', $string) != 0) { return true; }
  else { return false; }
}
function containsNumbers($string) {
  if (preg_match_all('/[0-9]/', $string) != 0) { return true; }
  else { return false; }

}
?>
