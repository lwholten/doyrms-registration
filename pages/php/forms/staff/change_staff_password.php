<?php 
// This file contains the code used to change a staff users password
// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');
require '../standard_functions.php';

// Variables
$staffID = $_COOKIE['dreg_staffID'];

// Checks the users old password and returns an error if it is incorrect
if (!checkStaffPassword(NULL, $staffID, $_POST['old_password_field'])) {
    customError(403, 'Your old password was incorrect. Please try again.');
    exit();
}

// Checks whether the new passwords match
if ($_POST['new_password_field'] === $_POST['confirm_new_password_field']) {
    // If they do match, it stores the new password
    $newPassword = $_POST['new_password_field'];
}
// Returns an error if they do not match
else {
    customError(403, 'New passwords do not match, please try again.');
    exit();
}

// Makes sure the new password is not blank, NULL or white space
if (verify($newPassword)) {

    // Saves whether the password meets the complexity requirements set in 'app.ini' 
    $passwordDetails =  meetsPasswordComplexityRequirements($newPassword);
    if (!$passwordDetails[0]) {
        customError(403, $passwordDetails[1]);
    }
    else {

        // Updates the password 
        updateStaffPassword($staffID, $newPassword);
        echo json_encode('Password updated successfully');

        // If the cookie used to determine whether the password prompt should appear is set to true (1)
        if ($_COOKIE['dreg_changePasswordPrompt'] == 1) {
            // Resets the cookie
            setcookie('dreg_changePasswordPrompt', 0, time() + (86400 * 30), "/");
            // Refreshes the page
            header("Refresh:0");
        }

        exit();

    }

}
else {
    customError(403, 'Your new password does not meet the minimum password requirements');
    exit();
}

?>