<?php 
// This file is used to return the access level of the user that is currently signed in
session_start();
echo $_SESSION['staffAccessLevel'];
exit();
?>