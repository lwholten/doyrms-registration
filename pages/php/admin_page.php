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
    /*Makes the edit section appear for the selected record, passes the record id and location name as parameters*/
    /*record[0] --> The ID of that record | record[1] --> The name of that location*/
    echo "<td><button onclick=\"formatEditSection('locations_edit_section','$record[1]','$record[0]');\" class=\"blue_button edit_button\">Edit</button></td>";
    echo "</tr>";
  }
}

/*Used to add locations to the database*/
function addLocation($name, $description, $popularity) {
  /*Connects to the database*/
  $con = databaseConnect();
  /*SQL query used to upload the data to the table*/
  $query = "INSERT INTO Locations (LocationId, LocationName, Description, Popularity) VALUES (NULL, '$name', '$description', '$popularity')";
  /*Executes the sql code*/
  $con->query($query);
  /*Disconnects from the database*/
  $con->close();
  /*Prevents form resubmission using a javascript function*/
  echo "<script>if(window.history.replaceState){window.history.replaceState(null, null, window.location.href);}</script>";
}
/* Detects if a location is being added and runs the code */
if (isset($_POST['add_location'])) {
  addLocation($_POST['location_name'],$_POST['location_desc'],$_POST['location_pop']);
}

/*Used to change the name of a location*/
function changeLocationName($locationId, $newLocationName) {
  /*SQL query used to change the location popularity stored on the table*/
  $query = "UPDATE Locations SET LocationName=? WHERE LocationId=?";
  /*Connects to the database*/
  $con = databaseConnect();
  /*turns the query into a statement*/
  $stmt = $con->prepare($query);
  $stmt->bind_param("ss", $newLocationName, $locationId);
  /*Executes the statement code*/
  $stmt->execute();
  /*Disconnects from the database*/
  $con->close();
  /*Prevents form resubmission using a javascript function*/
  echo "<script>if(window.history.replaceState){window.history.replaceState(null, null, window.location.href);}</script>";
}
/*Used to change the description of a location*/
function changeLocationDesc($locationId, $newLocationDesc) {
  /*SQL query used to change the location popularity stored on the table*/
  $query = "UPDATE Locations SET Description=? WHERE LocationId=?";
  /*Connects to the database*/
  $con = databaseConnect();
  /*turns the query into a statement*/
  $stmt = $con->prepare($query);
  $stmt->bind_param("ss", $newLocationDesc, $locationId);
  /*Executes the statement code*/
  $stmt->execute();
  /*Disconnects from the database*/
  $con->close();
  /*Prevents form resubmission using a javascript function*/
  echo "<script>if(window.history.replaceState){window.history.replaceState(null, null, window.location.href);}</script>";
}
/*used to change the popularity of a location*/
function changeLocationPop($locationId, $newLocationPop) {
  /*SQL query used to change the location popularity stored on the table*/
  $query = "UPDATE Locations SET Popularity=? WHERE LocationId=?";
  /*Connects to the database*/
  $con = databaseConnect();
  /*turns the query into a statement*/
  $stmt = $con->prepare($query);
  $stmt->bind_param("ss", $newLocationPop, $locationId);
  /*Executes the statement code*/
  $stmt->execute();
  /*Disconnects from the database*/
  $con->close();
  /*Prevents form resubmission using a javascript function*/
  echo "<script>if(window.history.replaceState){window.history.replaceState(null, null, window.location.href);}</script>";
}
/* Detects it the locations description is being changed */
if (isset($_POST['change_location_name'])) {
  changeLocationName($_POST['location_id'],$_POST['new_location_name']);
}
/* Detects it the locations description is being changed */
if (isset($_POST['change_location_desc'])) {
  changeLocationDesc($_POST['location_id'],$_POST['new_location_desc']);
}
/* Detects it the locations popularity is being changed */
if (isset($_POST['change_location_pop'])) {
  changeLocationPop($_POST['location_id'],$_POST['new_location_pop']);
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

/*Executed when the page is first loaded*/
onPageLoad();
?>
