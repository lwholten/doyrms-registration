<?php 
// This file contains the code used to login a staff user
// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');
require 'standard_functions.php';

// Functions
// Checks the staff users username exists
function checkStaffExists($username) {
    global $ini;
    // Used to check whether a staff user exists within the database
    $query = "SELECT CASE WHEN EXISTS (SELECT * FROM Staff WHERE Username=?) THEN 1 ELSE 0 END";
    // Connects to the database
    $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
    // Prepares and executes the statement
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    // Binds the result to a variable
    $stmt->bind_result($result);
    $stmt->fetch();
    // Disconnects from the database
    $con->close();
  
    // Returns whether the staff user exists
    return $result;
    if ($result == 1) {
        return true;
    }
    else {
        return false;
    }
}
// Fetches the staff users ID and Access Level
function fetchStaffDetails($username) {
    global $ini;
    // used to get the access level and id associated with a staff user
    $query = "SELECT Staff.StaffID, Staff.AccessLevel FROM Staff WHERE Username=? LIMIT 1";
    // Connects to the database
    $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
    // Prepares and executes the statement
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    // Binds the results to a variable
    $stmt->bind_result($result['staffID'], $result['staffAccessLevel']);
    $stmt->fetch();
    // Disconnects from the database
    $con->close();
  
    // Note that this function returns an associative ARRAY, NOT a STRING
    return $result;
}

// Function used to authorise a login to begin setup
function setupLogin() {

    // Assigns the username and staff ID to a cookie (it does not matter if the user sees this data)
    setcookie('dreg_staffUsername', $ini['setup_username'], time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie('dreg_staffID', 0, time() + (86400 * 30), "/"); // 86400 = 1 day

    // Enables setup mode once logged in
    setcookie('dreg_triggerSetupMode', 1, time() + (86400 * 30), "/");

    // Starts a session and stores that the staff user has logged in as well as their access level (users should NOT see this data)
    session_start();
    $_SESSION['loggedIn'] = 1;
    $_SESSION['staffAccessLevel'] = 3;
 
    // Returns successful
    echo true;
    exit();
}

// Main
// Checks whether setup mode is necessary
function checkForAdminAccount() {
    global $ini;
    // Query to check whether an administrator account is on the system
    $query = "SELECT CASE WHEN EXISTS (SELECT * FROM Staff WHERE AccessLevel=3 LIMIT 1) THEN 1 ELSE 0 END";
    // Connects to the database
    $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
    // Executes the statement
    $stmt = $con->prepare($query);
    $stmt->execute();
    // Binds the result to a variable
    $stmt->bind_result($result);
    $stmt->fetch();
    // Disconnects from the database
    $con->close();

    // Returns whether the staff user exists
    if ($result == 1) {
        return true;
    }
    else {
        return false;
    }
}

if (!checkForAdminAccount()) {
    if (($_POST['staff_username'] == $ini['setup_username']) && ($_POST['staff_password'] == $ini['setup_password'])) {
        setupLogin();
    }
    else {
        customError(422, 'There are no administrator accounts on the system. To continue, follow the setup guide.');
        return 0;
        exit();
    }
}

// Checks whether the staff user exists
if (checkStaffExists($_POST['staff_username']) && verify($_POST['staff_username'])) {
    $username = $_POST['staff_username'];
}
else {
    customError(422, 'The username or password you entered is incorrect. Please try again.');
    return 0;
    exit();
}

// Checks whether the staff users password is correct
if (checkStaffPassword($username, NULL, $_POST['staff_password'])) {
    // Fetches the staff users details
    $staffDetails = fetchStaffDetails($username);

    // Assigns the username and staff ID to a cookie (it does not matter if the user sees this data)
    setcookie('dreg_staffUsername', $username, time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie('dreg_staffID', $staffDetails['staffID'], time() + (86400 * 30), "/"); // 86400 = 1 day
    // Sets the setup cookie to false
    setcookie('dreg_triggerSetupMode', 0, time() + (86400 * 30), "/");

    // If the staffs password requires changing  (because it matches the password default)
    if ($_POST['staff_password'] == $ini['password_default']) {
        setcookie('dreg_changePasswordPrompt', 1, time() + (86400 * 30), "/");
    }
    else {
        setcookie('dreg_changePasswordPrompt', 0, time() + (86400 * 30), "/");
    }

    // Starts a session and stores that the staff user has logged in as well as their access level (users should NOT see this data)
    session_start();
    $_SESSION['loggedIn'] = 1;
    $_SESSION['staffAccessLevel'] = $staffDetails['staffAccessLevel'];

    // Returns successful
    echo true;
    exit();
}
else {
    customError(422, 'The username or password you entered is incorrect. Please try again.');
    return 0;
    exit();
}

?>