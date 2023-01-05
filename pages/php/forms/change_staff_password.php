<?php 
// This file contains the code used to change a staff users password
// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');
require 'standard_functions.php';

// Functions
// Also checks whether the password meets the password complexity requirements set in 'app.ini'
function meetsComplexityRequirements($password) {
    global $ini;

    // If the minimum password length is set
    // Compare the password length to the minimum password length and return an error if its too short
    if (!($ini['password_min_length'] === 'none') && (strlen($password) < $ini['password_min_length'])) {
         return [false, 'Passwords must be at least '.$ini['password_min_length'].' character'.(($ini['password_min_length'] > 1) ? 's' : '').' long']; } // The inline IF statement appends an S for plurals

    // If the maximum password length is set
    // Compare the password length to the maxmimum password length and return an error if its too long
    elseif (!($ini['password_max_length'] === 'none') && (strlen($password) > $ini['password_max_length'])) { 
        return [false, 'Passwords can be a maximum of '.$ini['password_max_length'].' character'.(($ini['password_max_length'] > 1) ? 's' : '').' long']; }

    // If the minimum capital letter count is set
    // Compare the number of capital letters in the new password to the minimum allowed and return an error if there is not enough
    elseif (!($ini['password_min_capitals'] === 'none') && ($ini['password_min_capitals'] > preg_match_all('/[A-Z]/', $password))) {
         return [false, 'Passwords must have at least '.$ini['password_min_capitals'].' capital letter'.(($ini['password_min_capitals'] > 1) ? 's' : '')]; }
    
    // If the maximum capital letter count is set
    // Compare the number of capital letters in the new password to the maximum allowed and return an error if there are too many
    elseif (!($ini['password_max_capitals'] === 'none') && ($ini['password_max_capitals'] < preg_match_all('/[A-Z]/', $password))) {
         return [false, 'Passwords may have no more than '.$ini['password_max_capitals'].' capital letter'.(($ini['password_max_capitals'] > 1) ? 's' : '')]; }

    // If the minimum number count is set
    // Compare the number of numbers in the new password to the minimum allowed and return an error if there is not enough
    elseif (!($ini['password_min_numbers'] === 'none') && ($ini['password_min_numbers'] > preg_match_all('/[0-9]/', $password))) { 
        return [false, 'Passwords must have at least '.$ini['password_min_numbers'].' number'.(($ini['password_min_numbers'] > 1) ? 's' : '')]; }
    
    // If the maximum number count is set
    // Compare the number of numbers in the new password to the maximum allowed and return an error if there are too many
    elseif (!($ini['password_max_numbers'] === 'none') && ($ini['password_max_numbers'] < preg_match_all('/[0-9]/', $password))) { 
        return [false, 'Passwords may have no more than '.$ini['password_max_numbers'].' number'.(($ini['password_max_numbers'] > 1) ? 's' : '')]; }


    // If the minimum special character count is set
    // Compare the number of special characters in the new password to the minimum allowed and return an error if there is not enough
    elseif (!($ini['password_min_symbols'] === 'none') && ($ini['password_min_symbols'] > preg_match_all('/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/', $password))) { 
        return [false, 'Passwords must have at least '.$ini['password_min_symbols'].' special character'.(($ini['password_min_symbols'] > 1) ? 's' : '')]; }
    
    // If the maximum special character count is set
    // Compare the number of special characters in the new password to the maximum allowed and return an error if there are too many
    elseif (!($ini['password_max_symbols'] === 'none') && ($ini['password_max_symbols'] < preg_match_all('/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/', $password))) { 
        return [false, 'Passwords may have no more than '.$ini['password_max_symbols'].' special character'.(($ini['password_max_symbols'] > 1) ? 's' : '')]; }

    // If all checks have been passed successfully
    else { return [true, NULL]; }

}

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
    $passwordDetails =  meetsComplexityRequirements($newPassword);
    if (!$passwordDetails[0]) {
        customError(403, $passwordDetails[1]);
    }
    else {
        updateStaffPassword($staffID, $newPassword);
        echo json_encode('Password updated successfully');
        exit();
    }

}
else {
    customError(403, 'Your new password does not meet the minimum password requirements');
    exit();
}

?>