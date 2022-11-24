<?php
/*Used to display the page, prevents users from seeing the page without logging in*/
function displayPage() {
  include("../../css/mainstyles.css");
  include("../../css/staff_page.css");
  include("../html/staff_page.html");
  include("../php/functions.php");
  $configs = include("../config/config.php");
}

/*Executes when this page is loaded*/
function onPageLoad() {
  /*Starts the PHP session so that the login status may be shared*/
  session_start();
  /* If a staff member has logged in*/
  /*  - prevents unauthorised users from pasting a URL*/
  if ($_SESSION["logged_in"] === True && ($_SESSION["access_level"] === "staff" || $_SESSION["access_level"] === "admin")) {
    displayPage();
  /* If a staff member has not logged in, they will be redirected*/
  } else {
    /*Alerts the user that they must log in*/
    echo '<script type="text/javascript">window.alert("DENIED: You must be logged in to access this page");</script>';
    /*Redirects the user back to the home page*/
    echo '<script type="text/javascript">window.location.href = "home_page.php";</script>';
  }
}

// Used to connect to the database
function databaseConnect() {
  // Create connection
  $con = new mysqli($config['hostname'], $config['username'], $config['password'], $config['database']);

  // Check connection
  if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
    return 0;
  }
  return $con;
}

/*Executed when the page is first loaded*/
onPageLoad();

/* Run the staff logout function if the staff user logs out*/
/* A form on the staff page is used to POST a variable when the logout button is pressed;
   This code executes when that variable has been set*/
if (isset($_POST['staff_sign_out'])) {
  /*Logs the staff user out*/
  session_destroy();
  echo '<script type="text/javascript">window.location.href = "home_page.php";</script>';
  exit();
}
?>
