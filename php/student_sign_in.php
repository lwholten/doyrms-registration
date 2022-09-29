<?php
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

// Executes the function if a student signs in
if (isset($_POST['student_sign_in_name'])) {
  studentSignIn();
}
?>
