<?php 
// This file is used to add locations to the system
// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');
require '../standard_functions.php';

// Functions
// Formats the keywords into a comma separated list
function formatKeyWords($keyWords) {
    // Removes all symbols (except commas)
    $keyWords = preg_replace('/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>\.\?\\\]/', '', $keyWords);
    // Replaces all commas with spaces with just commas
    // Then replaces all spaces with commas (in case the list was given as: 'this that this' instead of 'this, that, this')
    return preg_replace('/ /', '/,/', preg_replace('/, /', '/,/', $keyWords));
}
// Adds a location to the system
function addLocation($locationName, $keyWords, $description) {
    global $ini;

    // Query used to insert the location
    $query = "INSERT INTO `Locations` (`LocationID`, `LocationName`, `KeyWords`, `Description`) VALUES (NULL, ?, ?, ?)";
    // Connects to the database
    $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
    // Prepares and executes the insert statement
    $stmt = $con->prepare($query);
    $stmt->bind_param("sss", $locationName, $keyWords, $description);
    $stmt->execute();
    // Disconnects from the database
    $con->close();
  
    // Returns whether the INSERT was successful
    return true;
}

// Main
// Gets the location name and checks if it is valid
if (verify($_POST['location_field'])) {

    // If it contains no numbers or symbols
    if (!containsSymbols($_POST['location_field']) && !containsNumbers($_POST['location_field'])) {

        // Checks whether the location already exists
        if (!locationExists($_POST['location_field'])) {
            $location = ucwords($_POST['location_field']);
        }
        else {
            customError(403, 'The location entered already exists');
            exit();
        }
        
    }
    else {
        customError(403, 'The location name cannot contain numbers or symbols');
        exit();
    }

}
else {
    customError(403, 'The location name is invalid');
    exit();
}

// Gets the key words
if (verify($_POST['keywords_field'])) {
    $keyWords = formatKeyWords($_POST['keywords_field']);
}
else {
    $keyWords = NULL;
}

// Gets the description
if (verify($_POST['description_field'])) { $description = $_POST['description_field']; }
else { $description = NULL; }

// Finally, adds the location to the database
if (addLocation($location, $keyWords, $description)) {
    echo json_encode("the location has been added successfully");
    exit();
}
?>