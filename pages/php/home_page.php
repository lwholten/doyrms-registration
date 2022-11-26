<?php
/*Initiates a session when the page is first loaded*/
session_start();
/*Includes all files used in the main page*/
include("../../css/mainstyles.css");
include("../../css/main_page.css");
include("../html/home_page.html");

// Used to connect to the database
function databaseConnect() {
  // Create connection
  $con = new mysqli('localhost', 'dreg_user', 'epq', 'dregDB');

  // Check connection
  if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
    return 0;
  }
  return $con;
}
// Functions used for the staff login process
function checkStaffExists($username) {
  // Used to get the access level associated with the staff username
  $query = "SELECT CASE WHEN EXISTS (SELECT * FROM Staff WHERE Username=?) THEN 1 ELSE 0 END";
  // Connects to the database
  $con = databaseConnect();
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
  // Used to verify  associated with the staff username
  $query = "SELECT Salt, Hash FROM Staff WHERE Username=?";
  // Connects to the database
  $con = databaseConnect();
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
  // used to get the access level and id associated with a staff user
  $query = "SELECT Staff.StaffID, Staff.AccessLevel FROM Staff WHERE Username=? LIMIT 1";
  // Connects to the database
  $con = databaseConnect();
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
      echo "password correct";
      // initiates a PHP session and binds the username
      session_start();
      $_SESSION['staffUsername'] = $_POST['staff_username'];
      // Gets the staff users ID and access level from the database
      $staffDetails = fetchStaffDetails($_POST['staff_username']);
      // Binds the staff details to the session
      $_SESSION['staffID'] = $staffDetails['staffID'];
      $_SESSION['staffAccessLevel'] = $staffDetails['staffAccessLevel'];
      // Sets the logged in state to true
      $_SESSION['loggedIn'] = 1;

      // Redirects the staff user to the correct page
      if ($staffDetails['staffAccessLevel'] === 3) {
        // Admin page
        echo '<script type="text/javascript">window.location.href = "admin_page.php";</script>';
      }
      elseif ($staffDetails['staffAccessLevel'] === 1 || $staffDetails['staffAccessLevel'] === 2) {
        // Staff page
        echo '<script type="text/javascript">window.location.href = "staff_page.php";</script>';
      }
    }
    // If the password is incorrect
    else {
      unset($correct);
      exit("The password was incorrect");
    }
  }
  // If they do not exist
  else {
    unset($exists);
    exit("Could not find account on database");
  }
}
// Executes the function if a student signs in
?>
