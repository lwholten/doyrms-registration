<?php 
// This file is used to add events to the system
// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');
require '../standard_functions.php';

// Functions
// Adds a staff user to the system
function addEvent($event, $locationID, $start, $end, $deviation, $eventDays, $signInEvent) {
    global $ini;

    // Query used to insert the location
    $query = "INSERT INTO `Events` (`EventID`, `Event`, `LocationID`, `StartTime`, `EndTime`, `Deviation`, `Days`, `Alerts`, `SignInEvent`) VALUES (NULL, ?, ?, ?, ?, ?, ?, 1, ?)";
    // Connects to the database
    $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
    // Prepares and executes the insert statement
    $stmt = $con->prepare($query);
    $stmt->bind_param("sissisi", $event, $locationID, $start, $end, $deviation, $eventDays, $signInEvent);
    $stmt->execute();
    // Disconnects from the database
    $con->close();
  
    // Returns whether the INSERT was successful
    return true;
}

// Main
// Gets the event name and checks if it is valid
if (verify($_POST['event_field'])) {

    // If it contains no numbers or symbols
    if (!containsNumbers($_POST['event_field']) && !containsSymbols($_POST['event_field'])) {

        // Checks whether the event already exists
        if (!eventExists($_POST['event_field'])) {
            $event = ucwords($_POST['event_field']);
        }
        else {
            customError(403, 'The event entered already exists');
            exit();
        }
        
    }
    else {
        customError(403, 'The event name cannot contain numbers or symbols');
        exit();
    }

}
else {
    customError(403, 'The event name is invalid');
    exit();
}

// Gets the event trigger
if ($_POST['event_trigger'] == 'in') {
    $signInEvent = 1;
}
else {
    $signInEvent = 0;
}

// Gets the locationID if the event trigger is sign out
if (verify($_POST['location_field']) && $signInEvent == 0) {

    // If it contains no numbers or symbols
    if (!containsSymbols($_POST['location_field']) && !containsNumbers($_POST['location_field'])) {

        $locationID = fetchLocationID(ucwords($_POST['location_field']));

    }
    else {
        customError(403, 'The location name cannot contain numbers or symbols');
        exit();
    }

}
// If the event trigger is on a sign out, the location is required
else if ($signInEvent == 0) {
    customError(403, 'The location name is invalid');
    exit();
}
// The locationID is null for sign in events
else {
    $locationID = NULL;
}

// Gets the days where the event is active
$eventDays = "";
$days = ['mon','tue','wed','thu','fri','sat','sun'];
// Iterates through each day of the week and appends the days value if it has been selected
foreach($days as $day) {
    if (isset($_POST[$day])) {
        $eventDays .= $_POST[$day];
    }
};
// If no days are selected
if (strlen($eventDays) == 0 || is_null($eventDays) ) {
    customError(403, 'At least one day must be selected');
    exit();
}

// Gets the start time
if (verify($_POST['start_time_field'])) { $start = $_POST['start_time_field']; }
else {
    customError(403, 'The event must have a start time');
    exit();
}
// Gets the end time
if (verify($_POST['end_time_field'])) { $end = $_POST['end_time_field']; }
else {
    customError(403, 'The event must have a end time');
    exit();
}

// Gets the deviation
if (verify($_POST['new_event_timing'])) {
    // Error checks for deviation value
    if ($_POST['new_event_timing'] > 45) {
        $deviation = 45;
    }
    else if ($_POST['new_event_timing'] < 0) {
        $deviation = 0;
    }
    else {
        $deviation = $_POST['new_event_timing'];
    }
}
else {
    $deviation = 0;
}


// Finally, adds the event to the database
if (addEvent($event, $locationID, $start, $end, $deviation, $eventDays, $signInEvent)) {
    echo json_encode("the event has been added successfully");
    exit();
}
?>