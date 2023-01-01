<?php
// This file contains the code that is executed when the home page is loaded

// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');

// Functions used for the staff login process
function checkStaffExists($username) {
  global $ini;
  // Used to check whether a staff user exists within the database
  $query = "SELECT CASE WHEN EXISTS (SELECT * FROM Staff WHERE Username=?) THEN 1 ELSE 0 END";
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

  // Note that an integer is returned, not a string
  return $result;
}
function checkStaffPassword($username, $password) {
  global $ini;
  // Used to verify  associated with the staff username
  $query = "SELECT Salt, Hash FROM Staff WHERE Username=?";
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // Prepares and executes the statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("s", $username);
  $stmt->execute();
  // Binds the result to their respective variables
  $stmt->bind_result($result['salt'], $result['hash']);
  $stmt->fetch();
  // Disconnects from the database
  $con->close();

  // Concatinates the accounts salt to the start of the password
  $saltedPassword = $result['salt'].$password;
  // Verifies the password and returns a boolean value
  if (password_verify($saltedPassword, $result['hash'])) {
      unset($result);
      return 1;
  } else {
      unset($result);
      return 0;
  }
}
function fetchStaffDetails($username) {
  global $ini;
  // used to get the access level and id associated with a staff user
  $query = "SELECT Staff.StaffID, Staff.AccessLevel FROM Staff WHERE Username=? LIMIT 1";
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // Prepares and executes the statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("s", $username);
  $stmt->execute();
  // Binds the results to a variable
  $stmt->bind_result($result['staffID'], $result['staffAccessLevel']);
  $stmt->fetch();
  // Disconnects from the database
  $con->close();

  // Note that this function returns an INTEGER, NOT a STRING
  return $result;
}
function logStaffSignIn($staffID) {
  global $ini;
  // This function is used to save a log when a staff user signs in
  // It also sets the active state of this user to true

  // An array of SQL queries used to update the staff users login status
  $queries = array(
    "INSERT INTO StaffLog ( StaffID, SignedIn, LogTime ) VALUES (?, 1, CURRENT_TIMESTAMP)",
    "UPDATE Staff SET Active=1, LastActive=CURRENT_TIMESTAMP WHERE StaffID=?"
  );

  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // Iterates through the array and executes the queries
  foreach ($queries as $query) {
    // turns the query into a statement
    $stmt = $con->prepare($query);
    // Binds the staffID to the query
    $stmt->bind_param("i", $staffID);
    // Executes the statement code
    $stmt->execute();
  }
  // Disconnects from the database
  $con->close();
}

// Main (staff sign in)
// Initiates a session when the page is first loaded
session_start();
// Includes all files used in the main page
include("../html/home_page.html");

// Executes if a staff login is detected
if (isset($_POST['staff_login_form'])) {

  // Checks if the staff user exists
  $exists = checkStaffExists($_POST['staff_username']);
  // If they exist
  if ($exists === 1) {
    unset($exists);
    $correct = checkStaffPassword($_POST['staff_username'], $_POST['staff_password']);
    // If the password is correct
    if ($correct === 1) {
      unset($correct);
      // initiates a PHP session and binds the username
      if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
      }
      $_SESSION['staffUsername'] = $_POST['staff_username'];
      // Gets the staff users ID and access level from the database
      $staffDetails = fetchStaffDetails($_POST['staff_username']);
      // Binds the staff details to the session
      $_SESSION['staffID'] = $staffDetails['staffID'];
      $_SESSION['staffAccessLevel'] = $staffDetails['staffAccessLevel'];
      // Sets the home section to be the active section
      $_SESSION["activeSection"] = 'm1';
      $_SESSION["activeButton"] = 's1';
      // Sets the logged in state to true and logs the user in
      logStaffSignIn($staffDetails['staffID']);
      $_SESSION['loggedIn'] = 1;

      // Redirects the staff user to the correct page
      if ($staffDetails['staffAccessLevel'] >= 3) {
        // Admin page
        echo '<script type="text/javascript">window.location.href = "admin_page.php";</script>';
      }
      elseif ($staffDetails['staffAccessLevel'] >= 1) {
        // Staff page
        echo '<script type="text/javascript">window.location.href = "staff_page.php";</script>';
      }
    }
    // If the password is incorrect
    else {
      unset($correct);
      echo "<script>notification('The username or password you entered were incorrect','validation',2000)</script>";
      exit();
    }
  }
  // If they do not exist
  else {
    unset($exists);
    echo "<script>notification('The username or password you entered were incorrect','validation',2000)</script>";
    exit();
  }
}

// Functions used for the user sign out process
function checkLocationExists($location) {
  global $ini;
  // Used to check whether a location exists within the database
  $query = "SELECT CASE WHEN EXISTS (SELECT * FROM Locations WHERE Locations.LocationName=?) THEN 1 ELSE 0 END";
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

  return $result;
}
function checkUserExists($name) {
  global $ini;
  // Splits the name input into first and last names using a temporary array
  $tempArray = explode(" ", $name);
  $fname = $tempArray[1];
  $lname = $tempArray[2];
  unset($tempArray);

  // Used to check whether a user exists in the database
  $query = "SELECT CASE WHEN EXISTS (SELECT * FROM Users WHERE Users.Forename=? AND Users.Surname=?) THEN 1 ELSE 0 END";
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

  return $result;
}
function checkUserSignedIn($name) {
  global $ini;
  // This function is used to check whether a user is already signed in

  // Splits the name input into first and last names using a temporary array
  $tempArray = explode(" ", $name);
  $fname = $tempArray[1];
  $lname = $tempArray[2];
  unset($tempArray);
  // Used to check whether a the user is already signed in
  $query = "SELECT CASE WHEN EXISTS (SELECT * FROM Users WHERE Users.Forename=? AND Users.Surname=? AND Users.LocationID IS NULL) THEN 1 ELSE 0 END";
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

  // Returns 1 if the user is signed in, 0 if the user is signed out
  return $result;
}
function fetchEventID($location) {
  global $ini;
  // This function uses the location and nature of a sign in/out to decide whether the user has signed out for an event or not
  // NOTES:
  // $location -> STRING: Must be the name of a location
  // $nature -> BOOLEAN: Must determine the nature of the event, whether signing in (1) or signing out (0)
  // SQL query used to get all events the correspond to the time, location and nature of the user sign in/out
  $query = "SELECT EventID, StartTime, EndTime, Deviation FROM Events WHERE LocationID=(SELECT LocationID FROM Locations WHERE LocationName=?) AND Days LIKE ? ORDER BY StartTime ASC LIMIT 5";
  // Gets the current day and sets it to the corresponding 'day' value for the event
  // Concatinates '%' to the beginning and end so that the 'LIKE' statement functions correctly
  // Note: date('w') returns a numerical value for the current day of the week
  $day = '%'.substr("MTWRFUS", date('w')-1, date('w')-2).'%';
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // Prepares and executes the statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("ss", $location, $day);
  $stmt->execute();

  $result = $stmt->get_result();
  while ($row = $result->fetch_array(MYSQLI_NUM)) {
    // NOTES:
    // for $row[n] when n=0 -> EventID, n=1 -> StartTime, n=2 -> EndTime, n=3 -> Deviation
    if ($row[3] === NULL || $row[3] === "") {      echo "<script>window.alert('asdasd')</script>";
      $deviation = 0;
    }
    else {
      $deviation = $row[3];
    }
    // Calculates the early and late times by adding and subtracting the deviation time respectively
    $earlyTime = date('H:i:s', strtotime($row[1]) - (60*$deviation));
    $lateTime = date('H:i:s', strtotime($row[2]) + (60*$deviation));
    // Sets the start and end times for the event using the data fetched from the table
    $startTime = $row[1];
    $endTime = $row[2];
    // Gets the current time
    $currentTime = date('H:i:s', time());

    // Checks whether the user is early for this event
    if ($currentTime >= $earlyTime && $currentTime <= $startTime) {
      // Calculates how early the user is
      // Time early is stored as a positive integer
      $minutesEarly = intval(date('i', (strtotime($startTime) - strtotime($currentTime))));
      // Returns the eventID, breaking the while loop
      return [$row[0], $minutesEarly];
      exit();
    }
    else if ($currentTime >= $startTime && $currentTime <= $endTime) {
      // Returns the eventID, breaking the while loop
      return [$row[0], 0];
      exit();
    }
    else if ($currentTime >= $endTime && $currentTime <= $lateTime) {
      // Calculates how late the user is
      // Lateness is stored as a negative integer
      $minutesLate = -1*intval(date('i', (strtotime($LateTime) - strtotime($currentTime))));
      // Returns the eventID, breaking the while loop
      return [$row[0], $minutesLate];
      exit();
    }
  }
  // Disconnects from the database
  $con->close();
  // Returns the EventID and MinutesLate as NULL since no event was triggered
  return [NULL, NULL];
}
function userSignOut($name, $location) {
  global $ini;
  // Splits the name input into first and last names using a temporary array
  $tempArray = explode(" ", $name);
  $fname = $tempArray[1];
  $lname = $tempArray[2];
  unset($tempArray);
  // Fetches an eventID if a user triggers an event on signing out, if an event did not trigger '[NULL, 0]' is returned
  // Note that the '1' implies that it is a 'sign out event'
  $eventDetails = fetchEventID($location);
  // SQL query used to create the log
  $logQuery = "INSERT INTO Log ( UserID, LocationID, LogTime, EventID, MinutesLate, Auto ) SELECT  Users.UserID, Locations.LocationID, CURRENT_TIMESTAMP, ?, ?, 0 FROM Users, Locations WHERE Users.Forename=? AND Users.Surname=? AND Locations.LocationName=?";
  // SQL query used to update the users location
  $locationQuery = "UPDATE Users SET LastActive=CURRENT_TIMESTAMP, LocationID=(SELECT LocationID FROM Locations WHERE LocationName=?) WHERE Forename=? AND Surname=?";
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // turns the log query into a statement
  $logStmt = $con->prepare($logQuery);
  $logStmt->bind_param("iisss", $eventDetails[0], $eventDetails[1], $fname, $lname, $location);
  // Executes the statement code
  $logStmt->execute();
  // turns the location query into a statement
  $locationStmt = $con->prepare($locationQuery);
  $locationStmt->bind_param("sss", $location, $fname, $lname);
  // Executes the statement code
  $locationStmt->execute();
  // Disconnects from the database
  $con->close();
  return true;
}
function userSignIn($name, $auto) {
  global $ini;
  // Splits the name input into first and last names using a temporary array
  $tempArray = explode(" ", $name);
  $fname = $tempArray[1];
  $lname = $tempArray[2];
  unset($tempArray);
  // SQL query used to create a user log
  $logQuery = "INSERT INTO Log ( UserID, LocationID, LogTime, EventID, MinutesLate, Auto ) SELECT  Users.UserID, NULL, CURRENT_TIMESTAMP, NULL, NULL, ? FROM Users WHERE Users.Forename=? AND Users.Surname=?";
  // SQL query used to update the users location
  $locationQuery = "UPDATE Users SET LastActive=CURRENT_TIMESTAMP, LocationID=(SELECT LocationID FROM Locations WHERE LocationName=?) WHERE Forename=? AND Surname=?";
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // turns the log query into a statement
  $logStmt = $con->prepare($logQuery);
  $logStmt->bind_param("iss", $auto, $fname, $lname);
  // Executes the statement code
  $logStmt->execute();
  // turns the location query into a statement
  $locationStmt = $con->prepare($locationQuery);
  $locationStmt->bind_param("sss", $location, $fname, $lname);
  // Executes the statement code
  $locationStmt->execute();
  // Disconnects from the database
  $con->close();
  return true;
}

// Main (User login/logout)
// Executes if a user sign out is detected
if (isset($_POST['user_sign_out'])) {
  // If the user and location exist within the database
  $userExists = checkUserExists($_POST['sign_out_field']);
  $locationExists = checkLocationExists($_POST['sign_out_locations_field']);
  // If they exist
  if ($userExists === 1 && $locationExists === 1) {
    unset($userExists, $locationExists);
    $signedIn = checkUserSignedIn($_POST['sign_out_field']);
    // If the user is not signed in, sign them in automatically then sign them out
    if ($signedIn === 0) {
      unset($signedIn);
      userSignIn($_POST['sign_out_field'],1);
      $success = userSignOut($_POST['sign_out_field'], $_POST['sign_out_locations_field']);
    }
    // If the user is already signed in, don't automatically sign them in
    elseif ($signedIn === 1) {
      unset($signedIn);
      $success = userSignOut($_POST['sign_out_field'], $_POST['sign_out_locations_field']);
    }
    // If this was successful, it will relay the message to the user
    if ($success) {
      echo "<script>notification('You have been signed out successfully!','success',2000)</script>";
      unset($success);
    }
    // If this was unsuccessful, it will relay the message to the user and output an error
    else {
      echo "<script>notification('Something went wrong, please try again later','validation',3000)</script>";
      echo "<script>console.error('The system could not sign a user out: The function \'CreateUserLog()\' was unsuccessful!')</script>";
      unset($success);
    }
  }
  // If the location exists but not the user
  elseif ($userExists === 0 && $locationExists === 1) {
    unset($userExists, $locationExists);
    echo "<script>notification('Please make sure the name you entered is valid','validation',2000)</script>";
    exit();
  }
  // If the user exists but not the location
  elseif ($userExists === 1 && $locationExists === 0) {
    unset($userExists, $locationExists);
    echo "<script>notification('Please make sure the location you selected is valid','validation',2000)</script>";
    exit();
  }
  // If neither the location nor user exists
  else {
    unset($userExists, $locationExists);
    echo "<script>notification('Please make sure the name and location you entered are valid','validation',2000)</script>";
    exit();
  }
}
// Executes if a user sign in is detected
if (isset($_POST['user_sign_in'])) {
  // If the user exists in the database
  $userExists = checkUserExists($_POST['sign_in_field']);
  // If they exist
  if ($userExists === 1) {
    unset($userExists);
    // Checks whether the user is already signed in by searching for an uncompleted log
    // If there are no uncompleted logs, it can be assumed that the user is signed in
    $userSignedIn = checkUserSignedIn($_POST['sign_in_field']);
    // If the user is not already signed in, sign them in
    if ($userSignedIn === 0) {
      unset($userSignedIn);
      $success = userSignIn($_POST['sign_in_field'],0);
      // If this was successful, it will relay the message to the user
      if ($success) {
        echo "<script>notification('You have been signed in successfully!','success',2000)</script>";
        unset($success);
      }
      // If this was unsuccessful, it will relay the message to the user and output an error
      else {
        echo "<script>notification('Something went wrong, please try again later','error',3000)</script>";
        echo "<script>console.error('The system could not sign a user out: The function \'UpdateUserLog()\' was unsuccessful!')</script>";
        unset($success);
      }
    }
    // If the user is already signed in, relay a message to the user
    elseif ($userSignedIn === 1) {
      unset($userSignedIn);
      echo "<script>notification('It looks like you are already signed in!','warning',2000)</script>";
      exit();
    }
    else {
      unset($userSignedIn);
      echo "<script>notification('Sorry, we can't sign you in at the moment, please try again later.','error',3000)</script>";
      exit();
    }
  }
  else {
    unset($userExists);
    echo "<script>notification('Please make sure the name you entered is valid','validation',2000)</script>";
    exit();
  }
}
?>
