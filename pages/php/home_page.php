<?php
// The PHP code contained within this file is executed when the home page is loaded or refreshed
// This file contains the code required for staff users to log into the staff page

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
