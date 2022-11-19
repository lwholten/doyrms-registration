<?php
/*Used to display the page, prevents users from seeing the page without logging in*/
function displayPage() {
  /*Note that the admin page imports the css and js used by the staff page*/
  include("../../css/mainstyles.css");
  include("../../css/staff_page.css");
  include("../../css/admin_page.css");
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

/*Used to fill the locations table with data from the database*/
function fillLocationsTable() {
  /*Connects to the database*/
  $con = databaseConnect();
  /*SQL code to get the table data*/
  $sql = "SELECT * FROM `Locations`";
  /*Saves the result of the SQL code to a variable*/
  $result = $con->query($sql);
  /*Disconnects from the database*/
  $con->close();

  /*Iterates through the table records and displays them on the web page's table*/
  while($record = $result -> fetch_array(MYSQLI_NUM)) {
    echo "<tr>";
    foreach ($record as $value) {
      echo "<td> $value </td>";
    }
    /*Echoes the edit button for this record, it passes the location name as a parameter so it may be used in the edit section's title*/
    echo "<td><button onclick=\"formatEditSection('locations_edit_section','$record[1]');\" class=\"blue_button edit_button\">Edit</button></td>";
    echo "</tr>";
  }
}

/*Used to add locations to the database*/
function addLocation($name, $description, $popularity) {
  /*Connects to the database*/
  $con = databaseConnect();
  /*SQL code to upload the data to the table*/
  $sql = "INSERT INTO `Locations` (`LocationId`, `LocationName`, `Description`, `Popularity`) VALUES (NULL, '$name', '$description', '$popularity')";
  /*Executes the sql code*/
  $con->query($sql);
  /*Disconnects from the database*/
  $con->close();
  /*Prevents form resubmission using a javascript function*/
  echo "<script>if(window.history.replaceState){window.history.replaceState(null, null, window.location.href);}</script>";
}
/*Executed when the page is first loaded*/
onPageLoad();

// Executes the function if a member of staff signs in
if (isset($_POST['location_name']) && isset($_POST['location_desc']) && isset($_POST['location_pop'])) {
  addLocation($_POST['location_name'],$_POST['location_desc'],$_POST['location_pop']);
}

/* Run the admin logout function if the admin user logs out*/
/* A form on the admin page is used to POST a variable when the logout button is pressed;
   This code executes when that variable has been set*/
if (isset($_POST['admin_sign_out'])) {
  /*Logs the admin user out*/
  session_destroy();
  echo '<script type="text/javascript">window.location.href = "home_page.php";</script>';
  exit();
}
?>
