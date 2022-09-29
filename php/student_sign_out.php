<?php
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

// Executes the function if a student signs in
if (isset($_POST['student_sign_out_name']) && isset($_POST['student_sign_out_location'])) {
  studentSignOut();
}
?>
