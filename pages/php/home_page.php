<?php
include("../php/functions.php");
include("../../css/mainstyles.css");
include("../../css/main_page.css");
include("../html/home_page.html");

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
