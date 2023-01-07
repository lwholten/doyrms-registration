<?php
// This file contains the code that is executed when a request is sent to the server to remove a user
// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');
require '../standard_functions.php';
// Functions
// Used to unrestrict a user
function deleteUser($name) {
    global $ini;

    // Fetches the users ID
    $userID = fetchUserID($name, 'none');

    // Query used to unrestrict the user
    $queries = [
    "log" => "DELETE FROM Log WHERE UserID=?",
    "away" => "DELETE FROM AwayUsers WHERE UserID=?",
    "restricted" => "DELETE FROM RestrictedUsers WHERE UserID=?",
    "users" => "DELETE FROM Users WHERE UserID=?"
    ];
    // Connects to the database
    $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
    // Prepares and executes each query
    foreach ($queries as $key => $query) {
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $userID);
        $stmt->execute();
    }
    // Disconnects from the database
    $con->close();

    // Returns whether the INSERT was successful
    return true;
}

// Variables
// The staff username
$name = $_POST['name_field'];

// Main
// If the staff user exists, delete their account
if (userExists(explode(" ", $name)[0], explode(" ", $name)[1])) {
  if (deleteUser($name)) {
    echo json_encode('The user has been deleted successfully');
    exit();
  }
}
else {
  customError(409, 'This user does not exist');
  exit();
}
?>
