<?php
// Executed if the staff login form is submitted
function staffLogin() {
  // converts the form POST array into useful variables
  $staff_username = $_POST['staff_username'];
  $staff_password = $_POST['staff_password'];

  // If username and/or password field are left empty
  if (empty($staff_username) or empty($staff_password)) {
    // add something here in the future for when input is left empty

    // I will create a javascript function to display a note in the form area
    // telling the user to fill in the box before submitting
    echo 'please input username and/or password';
  }
  else {
    // use this area to declare what must happen to the variable with the server
    // temporarily displays the input data on the website
    echo 'Staff Username: ',$staff_username;
    echo 'Staff Password: ',$staff_password;
  }

};

// Executes the function if a member of staff signs in
if (isset($_POST['staff_username']) && isset($_POST['staff_password'])) {
  staffLogin();
}
?>
