<?php
/*Used to display the page, prevents users from seeing the page without logging in*/
function displayPage() {
  include("../../css/styles.css");
  include("../html/admin_page.html");
  include("../php/functions.php");
}

/*Executes when this page is loaded*/
function onPageLoad() {
  /*Starts the PHP session so that the login status may be shared*/
  session_start();
  /* If a staff member has logged in*/
  /*  - prevents unauthorised users from pasting a URL*/
  if ($_SESSION["logged_in"] === True && $_SESSION["access_level"] === "admin") {
    displayPage();
  /* If a staff member has not logged in, they will be redirected*/
  } else {
    /*Alerts the user that they must log in*/
    echo '<script type="text/javascript">window.alert("DENIED: You do not have access this page, please log in as an administrator");</script>';
    /*Redirects the user back to the home page*/
    echo '<script type="text/javascript">window.location.href = "home_page.php";</script>';
  }
}

onPageLoad();
?>
