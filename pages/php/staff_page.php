<?php
/*Used to display the page, prevents users from seeing the page without logging in*/
function displayPage() {
  include("../../css/mainstyles.css");
  include("../../css/staff_page.css");
  include("../html/staff_page.html");
}

/*Executes when this page is loaded*/
function onPageLoad() {
  /*Starts the PHP session so that the login status may be shared*/
  session_start();
  /* If a staff user has logged in*/
  /*  - prevents unauthorised users from accessing the page using a modified URL*/
  if ($_SESSION["loggedIn"] === 1 && $_SESSION["staffAccessLevel"] >= 1) {
    displayPage();
  /* If a staff user has not logged in, they will be redirected*/
  } else {
    /*Alerts the user that they must log in*/
    echo '<script type="text/javascript">window.alert("DENIED: You must be logged in as a staff user to access this page");</script>';
    /*Redirects the user back to the home page*/
    echo '<script type="text/javascript">window.location.href = "home_page.php";</script>';
  }
}

// Used to connect to the database
function databaseConnect() {
  // Connection details
  $servername = "localhost";
  $username = "dreg_user";
  $password = "epq";
  $database = 'dregDB';

  // Create connection
  $con = new mysqli($servername, $username, $password, $database);

  // Check connection
  if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
    return 0;
  }
  return $con;
}

/*Executed when the page is first loaded*/
onPageLoad();

function logStaffSignOut($staffID) {
  // This is used to log a staff user sign out

  // SQL query used to create the log
  $query = "UPDATE StaffLog SET SignOutTime=CURRENT_TIME, Complete=1 WHERE StaffID=? AND SignOutTime IS NULL AND Complete=0 ORDER BY SignInTime DESC LIMIT 1;";
  // Connects to the database
  $con = databaseConnect();
  // turns the query into a statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("i", $staffID);
  // Executes the statement code
  $stmt->execute();
  // Disconnects from the database
  $con->close();
}
/* Run the staff logout function if the staff user logs out*/
/* A form on the staff page is used to POST a variable when the logout button is pressed;
   This code executes when that variable has been set*/
if (isset($_POST['staff_sign_out'])) {
  /*Logs the staff user out*/
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
