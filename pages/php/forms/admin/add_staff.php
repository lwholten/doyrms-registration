<?php 
// This file is used to add staff users to the system
// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');
require '../standard_functions.php';

// Functions
// Adds a staff user to the system
function addStaffUser($username, $forename, $surname, $email, $accessLevel, $password) {
    global $ini;

    // Generates a new salt
    $salt = generateRandomSalt();
    // Concatinates the salt to the START of the password
    $saltedPassword = $salt.$password;
    // Hashes the salted password
    $hash = password_hash($saltedPassword, PASSWORD_BCRYPT);

    // Query used to insert the user
    $query = "INSERT INTO `Staff` (`StaffID`, `Username`, `Forename`, `Surname`, `Email`, `Salt`, `Hash`, `AccessLevel`, `LastChangedPassword`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP);";
    // Connects to the database
    $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
    // Prepares and executes the insert statement
    $stmt = $con->prepare($query);
    $stmt->bind_param("ssssssi", $username, $forename, $surname, $email, $salt, $hash, $accessLevel);
    $stmt->execute();
    // Disconnects from the database
    $con->close();
  
    // Returns whether the INSERT was successful
    return true;
}

// Main
// Checks whether the staff username has been filled
if (verify($_POST['username_field'])) {
    
    // Stores whether the username meets the username requirements set in 'app.ini' 
    $usernameDetails =  meetsUsernameComplexityRequirements($_POST['username_field']);
    if (!$usernameDetails[0]) {
        customError(403, $usernameDetails[1]);
    }
    else {

        // Checks whether the username is taken
        if (staffUserExists($_POST['username_field'])) {
            customError(403, 'The username entered is already taken');
            exit();
        }
        else {
            $username = $_POST['username_field'];
        }
    }

}
else {
    customError(403, 'The staff username is invalid');
    exit();
}

// Checks whether the first and last names are valid
if (verify($_POST['fname_field'] && verify($_POST['lname_field']))) {

    // Stores whether the last name meets the name requirements set in 'app.ini' 
    $fnameDetails =  meetsNameComplexityRequirements($_POST['fname_field']);
    if (!$fnameDetails[0]) { customError(403, $fnameDetails[1]); }
    else { $fname = ucwords(rtrim($_POST['fname_field'])); }

    // Stores whether the last name meets the name requirements set in 'app.ini' 
    $lnameDetails =  meetsNameComplexityRequirements($_POST['lname_field']);
    if (!$lnameDetails[0]) { customError(403, $lnameDetails[1]); }
    else { $lname = ucwords(rtrim($_POST['lname_field'])); }
}
else {
    customError(403, 'The staff users name is invalid');
    exit();
}

// Gets the staff user access level
if ($_POST['staff_access_level'] === 'staff') { $accessLevel = 1; }
else if ($_POST['staff_access_level'] === 'moderator') { $accessLevel = 2; }
else if ($_POST['staff_access_level'] === 'admin') { $accessLevel = 3; }
else { $accessLevel = 1; }

// Gets the staff users email
if (verify($_POST['email_field'])) {

    if (filter_var($_POST['email_field'], FILTER_VALIDATE_EMAIL)) {
        $email = $_POST['email_field'];
    } else {
        customError(403, 'The email address is invalid');
        exit();
    }

}
else {
    $email = NULL;
}

// Gets the password and ensures it meets the password complexity requirements
if (verify($_POST['password_field'])) {
    
    // Stores whether the password meets the complexity requirements set in 'app.ini' 
    $passwordDetails =  meetsPasswordComplexityRequirements($_POST['password_field']);
    if (!$passwordDetails[0]) {
        customError(403, $passwordDetails[1]);
    }
    else {
        $password = $_POST['password_field'];
    }

}
else {
    $password = $ini['password_default'];
}

// Finally, adds the staff user to the system
if (addStaffUser($username, $fname, $lname, $email, $accessLevel, $password)) {
    echo json_encode("the staff user has been added successfully");
}
?>