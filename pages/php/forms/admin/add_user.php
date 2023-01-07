<?php 
// This file is used to add staff users to the system
// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');
require '../standard_functions.php';

// Functions
// Used to add users to the system
function addUser($forename, $surname, $email, $gender, $roomNumber) {
    global $ini;
    // Generates the users initials using their forename and surname
    $initials = strtoupper($forename[0].$surname[0]);
  
    // SQL query used to change the location popularity stored on the table
    $query = "INSERT INTO `Users` (`UserID`, `Forename`, `Surname`, `Email`, `Gender`, `RoomNumber`, `Initials`, `LocationID`) VALUES (NULL, ?, ?, ?, ?, ?, ?, NULL)";
    // Connects to the database
    $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
    // turns the query into a statement
    $stmt = $con->prepare($query);
    $stmt->bind_param("ssssis", $forename, $surname, $email, $gender, $roomNumber, $initials);
    // Executes the statement code
    $stmt->execute();
    // Disconnects from the database
    $con->close();

    // Returns whether the INSERT was successful
    return true;
  }

// Main
// Checks whether the first and last names are valid
if (verify($_POST['fname_field'] && verify($_POST['lname_field']))) {

    // If the user does not already exist
    if (!userExists($_POST['fname_field'], $_POST['lname_field'])) {
        
        // Stores whether the last name meets the name requirements set in 'app.ini' 
        $fnameDetails =  meetsNameComplexityRequirements($_POST['fname_field']);
        if (!$fnameDetails[0]) { customError(403, $fnameDetails[1]); }
        else { $fname = ucwords(rtrim($_POST['fname_field'])); }
    
        // Stores whether the last name meets the name requirements set in 'app.ini' 
        $lnameDetails =  meetsNameComplexityRequirements($_POST['lname_field']);
        if (!$lnameDetails[0]) { customError(403, $lnameDetails[1]); }
        else { $lname = ucwords(rtrim($_POST['lname_field'])); }

    }
    // If the user already exists
    else {
        customError(403, 'This user already exists');
        exit();    
    }

}
else {
    customError(403, 'The users name is invalid');
    exit();
}

// Gets the users email
if (verify($_POST['email_field'])) {

    // If it is set
    if (filter_var($_POST['email_field'], FILTER_VALIDATE_EMAIL)) {
        $email = $_POST['email_field'];
    } else {
        customError(403, 'The email address is invalid');
        exit();
    }

}
else { $email = NULL; }

// Gets the users gender
if ($_POST['staff_access_level'] === 'm') { $gender = 'm'; }
else if ($_POST['staff_access_level'] === 'f') { $gender = 'f'; }
else if ($_POST['staff_access_level'] === 'o') { $gender = 'o'; }
else { $gender = 'o'; }

// Gets the users room number
if (verify($_POST['room_number_field'])) {
    // If it is set
    if (is_numeric($_POST['room_number_field']) && ($_POST['room_number_field'] <= 999)) { $roomNumber=$_POST['room_number_field']; }
    else {
        customError(403, 'The room number is invalid');
        exit();
    }
}
// If it is not set
else { $roomNumber = NULL; }

// Finally, adds the staff user to the system
if (addUser($fname, $lname, $email, $gender, $roomNumber)) {
    echo json_encode("the user has been added successfully");
    exit();
}
?>