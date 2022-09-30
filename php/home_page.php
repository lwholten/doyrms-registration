<?php
include("html/home_page.html");
include("php/database_functions.php");

// Executed if the staff login form is submitted
function staffLogin() {
  // converts the form POST array into useful variables
  $staff_username = $_POST['staff_username'];
  $staff_password = $_POST['staff_password'];

  // If username and/or password field are left empty
  if (empty($staff_username) or empty($staff_password)) {
    // Luke:
    // I will create a javascript function to display a note in the form area
    // telling the user to fill in the box before submitting
    echo 'please input username and/or password';
  }
  // If username and/or password fields are not left empty
  else {
    // use this area to declare what must happen to the variable with the server
    // temporarily displays the input data on the website
    echo 'Staff Username: ',$staff_username;
    echo 'Staff Password: ',$staff_password;

    // If username and password are correct
    if (verifyStaffLoginDetails($staff_username, $staff_password)) {
        // Creates a session so that the staff member can stay logged in
        // Allows variables to be used accross pages
        session_start();
        // stores the staff username for use in this session
        $_SESSION["staff_username"] = $staff_username;
        // Tells the server that the staff has logged in, the next page will use this as authentication
        $_SESSION["logged_in"] = True;
        // redirects the loged in user to the staff page
        header('Location: http://' . $_SERVER['HTTP_HOST'] . '/doyrms-registration/php/staff_page.php');
    }
    // If login details are incorrect
    else {
      // Luke:
      // I will create a javascript function to display an 'incorrect login info'
      // Message here
    }
  }

};
// Executed if the student sign in form is submitted
function studentSignIn() {
  // converts the form POST array into useful variables
  $student_name = $_POST['student_sign_in_name'];

  // If name field is left empty
  if (empty($student_name)) {
    // add something here in the future if box is left empty

    // I will create a javascript function to display a note in the form area
    // telling the user to fill in the box before submitting
    echo 'Please input your name';
  }
  else {
    // use this area to declare what must happen to the variable with the server
    // temporarily displays the input data on the website
    echo 'Student Name: ',$student_name;
  }

};
// Executed if the student sign in form is submitted
function studentSignOut() {
  // converts the form POST array into useful variables
  $student_name = $_POST['student_sign_out_name'];
  $student_location = $_POST['student_sign_out_location'];

  // If name and/or location fields are left empty
  if (empty($student_name) or empty($student_location)) {
    // add something here in the future for when input is left empty

    // I will create a javascript function to display a note in the form area
    // telling the user to fill in the box before submitting
    echo 'please input name and/or location';
  }
  else {
    // use this area to declare what must happen to the variable with the server
    // temporarily displays the input data on the website
    echo 'Student name: ',$student_name;
    echo 'Student location: ',$student_location;
  }

};

// Executes the function if a member of staff signs in
if (isset($_POST['staff_username']) && isset($_POST['staff_password'])) {
  staffLogin();
}
// Executes the function if a student signs in
if (isset($_POST['student_sign_in_name'])) {
  studentSignIn();
}
// Executes the function if a student signs out
if (isset($_POST['student_sign_out_name']) && isset($_POST['student_sign_out_location'])) {
  studentSignOut();
}
?>
