<?php
// This file contains the code that is executed when a request is sent to the server to remove a location
// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');
require '../standard_functions.php';

// Functions
// Used to delete a location
function deleteLocation($location) {
    global $ini;

    // Fetches the location ID
    $locationID = fetchLocationID($location, 'none');

    // Query used to remove the location
    $queries = [
    "log" => "DELETE FROM Log WHERE LocationID=? AND LocationID IS NOT NULL",
    // Note that it sets all users signed out to this location, current location to 'not specified (0)'
    "users" => "UPDATE Users SET LocationID=0 WHERE LocationID=? AND LocationID IS NOT NULL;",
    "events" => "DELETE FROM Events WHERE LocationID=? AND LocationID IS NOT NULL",
    "locations" => "DELETE FROM Locations WHERE LocationID=?"
    ];
    // Connects to the database
    $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
    // Prepares and executes each query
    foreach ($queries as $key => $query) {
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $locationID);
        $stmt->execute();
    }
    // Disconnects from the database
    $con->close();

    // Returns whether the INSERT was successful
    return true;
}

// Variables
// The location name
$location = $_POST['location_field'];

// Main
// If the location exists, delete their account
if (locationExists($location)) {
  if (deleteLocation($location)) {
    echo json_encode('The location has been deleted successfully');
    exit();
  }
}
else {
  customError(409, 'This location does not exist');
  exit();
}
?>
