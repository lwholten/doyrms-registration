<?php
// This file contains the code that is executed when a request is sent to the server to remove a event
// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');
require '../standard_functions.php';

// Functions
// Used to delete an event
function deleteevent($event) {
    global $ini;

    // Fetches the event ID
    $eventID = fetchEventID($event, 'none');

    // Query used to remove the event
    $queries = [
    "log" => "DELETE FROM Log WHERE eventID=?",
    "events" => "DELETE FROM Events WHERE eventID=?"
    ];
    // Connects to the database
    $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
    // Prepares and executes each query
    foreach ($queries as $key => $query) {
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $eventID);
        $stmt->execute();
    }
    // Disconnects from the database
    $con->close();

    // Returns whether the INSERT was successful
    return true;
}

// Variables
// The event name
$event = $_POST['event_field'];

// Main
// If the event exists, delete their account
if (eventExists($event)) {
  if (deleteEvent($event)) {
    echo json_encode('The event has been deleted successfully');
    exit();
  }
}
else {
  customError(409, 'This event does not exist');
  exit();
}
?>
