<?php
// This file contains the code that is executed when the staff page is loaded

// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');

// Used to display the page, prevents users from seeing the page without logging in
function displayPage() {
  include("../html/staff_page.html");
}

// Executes when this page is loaded
function onPageLoad() {
  // Starts the PHP session so that the login status may be shared
  session_start();
  //  If a staff user has logged in
  //   - prevents unauthorised users from accessing the page using a modified URL
  if ($_SESSION["loggedIn"] === 1 && $_SESSION["staffAccessLevel"] >= 1) {
    displayPage();
  //  If a staff user has not logged in, they will be redirected
  } else {
    // Alerts the user that they must log in
    echo '<script type="text/javascript">window.alert("DENIED: You must be logged in as a staff user to access this page");</script>';
    // Redirects the user back to the home page
    echo '<script type="text/javascript">window.location.href = "home_page.php";</script>';
  }
}

// Executed when the page is first loaded
onPageLoad();

function logStaffSignOut($staffID) {
  global $ini;
  // This function is used to save a log when a staff user signs out
  // It also sets the active state of this user to false

  // An array of SQL queries used to update the staff users login status
  $queries = array(
    "INSERT INTO StaffLog ( StaffID, SignedIn, LogTime ) VALUES (?, 0, CURRENT_TIMESTAMP)",
    "UPDATE Staff SET Active=0, LastActive=CURRENT_TIMESTAMP WHERE StaffID=?"
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
//  Run the staff logout function if the staff user logs out
//  A form on the staff page is used to POST a variable when the logout button is pressed; This code executes when that variable has been set
if (isset($_POST['staff_sign_out'])) {
  // Logs the staff user out
  if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
  }
  logStaffSignOut($_SESSION['staffID']);
  // Destroys the session, unsets all variables and unsets the logged in check
  session_destroy();
  session_unset();
  unset($_SESSION["loggedIn"]);
  $_SESSION = array();
  // Redirects back to the home page
  echo '<script type="text/javascript">window.location.href = "home_page.php";</script>';
  exit();
}
?>
