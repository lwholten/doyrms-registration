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
  if ($_SESSION["loggedIn"] == 1 && $_SESSION["staffAccessLevel"] >= 1) {
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

//  Run the staff logout function if the staff user logs out
//  A form on the staff page is used to POST a variable when the logout button is pressed; This code executes when that variable has been set
if (isset($_POST['staff_sign_out'])) {
  // Unsets all cookies
  if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time()-1000);
        setcookie($name, '', time()-1000, '/');
    }
  }

  // Destroys the session and any session variables
  session_destroy();
  session_unset();
  $_SESSION = array();
  // Redirects back to the home page
  echo '<script type="text/javascript">window.location.href = "home_page.php";</script>';
  exit();
}
?>
