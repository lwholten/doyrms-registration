<?php
/*Used to display the page, prevents users from seeing the page without logging in*/
function displayPage() {
  /*Note that the admin page imports the css and js used by the staff page*/
  include("../../css/mainstyles.css");
  include("../../css/staff_page.css");
  include("../../css/admin_page.css");
  include("../html/admin_page.html");

  /*Loads the navbar section that is currently active, that way if the page is refreshed
  the page will stay on the most recently selected navbar option - useful for when a form is submitted*/
  $activeSection = $_SESSION["activeSection"];
  $activeButton = $_SESSION["activeButton"];
  echo "<script>loadActiveSection('$activeSection', '$activeButton')</script>";
}

/*Executes when this page is loaded*/
function onPageLoad() {
  /*Starts the PHP session so that the login status may be shared*/
  session_start();
  /* If a staff user has logged in*/
  /*  - prevents unauthorised users from accessing the page using a modified URL*/
  if ($_SESSION["loggedIn"] === 1 && $_SESSION["staffAccessLevel"] >= 3) {
    displayPage();
  /* If a staff user has not logged in, they will be redirected*/
  } else {
    /*Alerts the user that they must log in*/
    echo '<script type="text/javascript">window.alert("DENIED: You do not have access this page, please log in as an administrator");</script>';
    /*Redirects the user back to the home page*/
    echo '<script type="text/javascript">window.location.href = "home_page.php";</script>';
  }
}

// Used to connect to the database
function databaseConnect() {
  // Connection details
  $servername = "localhost";
  $username = "dreg_user";
  $password = "epq";
  $database = 'dregDB';

  // Create connection
  $con = new mysqli($servername, $username, $password, $database);

  // Check connection
  if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
    return 0;
  }
  return $con;
}


/*Used to add users to the database*/
function addStaff($username, $password, $forename, $surname, $email, $accessLevel) {
  /*Data formatting:*/
  /*Makes the forname and surname uppercase before being added to the database*/
  $forename = ucwords($forename);
  $surname = ucwords($surname);

  /*If the variable is an empty string, make it NULL*/
  if ($email === "") { unset($email); }

  /*Generates a random 32 character salt*/
  $salt = bin2hex(random_bytes(16));
  /*Concatinates it to the START of the password*/
  $saltedPassword = $salt.$password;
  /*Hashes the salted password*/
  $hash = password_hash($saltedPassword, PASSWORD_BCRYPT);

  /*SQL query used to change the location popularity stored on the table*/
  $query = "INSERT INTO `Staff` (`StaffID`, `Username`, `Forename`, `Surname`, `Email`, `Salt`, `Hash`, `AccessLevel`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?)";
  /*Connects to the database*/
  $con = databaseConnect();
  /*turns the query into a statement*/
  $stmt = $con->prepare($query);
  $stmt->bind_param("sssssss", $username, $forename, $surname, $email, $salt, $hash, $accessLevel);
  /*Executes the statement code*/
  $stmt->execute();
  /*Disconnects from the database*/
  $con->close();
  /*Stores the active sidebar section to a variable so that when the page is refreshed it redirects the user to the section they had selected*/
  session_start();
  $_SESSION['activeSection'] = 'm11';
  $_SESSION['activeButton'] = 's11';
  /*Prevents form resubmission using a javascript function*/
  echo "<script>if(window.history.replaceState){window.history.replaceState(null, null, window.location.href);}</script>";
}
/* Detects if a user is being added and runs the code */
if (isset($_POST['add_staff'])) {
  addStaff($_POST['staff_username'], $_POST['staff_password'], $_POST['staff_forename'], $_POST['staff_surname'], $_POST['staff_email'], $_POST['staff_access_level']);
}


// Used to change a staff users password
function changeStaffPassword($staffID, $newPassword) {
  // Used to get the salt associated with a staff user
  $getSaltQuery = "SELECT Salt FROM Staff WHERE StaffID=? LIMIT 1;";
  // Used to update the old hash
  $updateHashQuery = "UPDATE Staff SET Hash=? WHERE staffID=?";
  // Connects to the database
  $con = databaseConnect();

  // Prepares and executes the first statement
  $stmt = $con->prepare($getSaltQuery);
  $stmt->bind_param("s", $staffID);
  $stmt->execute();
  // Binds the result to a variable
  $stmt->bind_result($salt);
  $stmt->fetch();
  unset($stmt);

  // Concatinates the salt to the START of the password
  $saltedPassword = $salt.$newPassword;
  // Hashes the salted password
  $hash = password_hash($saltedPassword, PASSWORD_BCRYPT);

  // Prepares the query used to update the hash
  $stmt = $con->prepare($updateHashQuery);
  $stmt->bind_param("ss", $hash, $staffID);
  // Executes the second statement, updating the hash
  $stmt->execute();

  // Disconnects from the database
  $con->close();
  /*Prevents form resubmission using a javascript function*/
  echo "<script>if(window.history.replaceState){window.history.replaceState(null, null, window.location.href);}</script>";
}

// Used to change a users database entries
function changeStaffEntry($staffID, $newValue, $fieldName) {
  /*SQL query(s) used to change the location popularity stored on the table*/
  if ($fieldName === "Username") {
    $query = "UPDATE Staff SET Username=? WHERE staffID=?";
  }
  elseif ($fieldName === "Forename") {
    $query = "UPDATE Staff SET Forename=? WHERE staffID=?";
    $newValue = ucwords($newValue);
  }
  elseif ($fieldName === "Surname") {
    $query = "UPDATE Staff SET Surname=? WHERE staffID=?";
    $newValue = ucwords($newValue);
  }
  elseif ($fieldName === "Email") {
    $query = "UPDATE Staff SET Email=? WHERE staffID=?";
  }
  elseif ($fieldName === "AccessLevel") {
    $query = "UPDATE Staff SET AccessLevel=? WHERE staffID=?";
  }
  /*Connects to the database*/
  $con = databaseConnect();
  /*turns the query into a statement*/
  $stmt = $con->prepare($query);
  $stmt->bind_param("ss", $newValue, $staffID);
  /*Executes the statement code*/
  $stmt->execute();
  /*Disconnects from the database*/
  $con->close();
  /*Stores the active sidebar section to a variable so that when the page is refreshed it redirects the user to the section they had selected*/
  session_start();
  $_SESSION['activeSection'] = 'm11';
  $_SESSION['activeButton'] = 's11';
  /*Prevents form resubmission using a javascript function*/
  echo "<script>if(window.history.replaceState){window.history.replaceState(null, null, window.location.href);}</script>";
}
/* Detects if the edit location form has been submitted */
if (isset($_POST['edit_staff_form'])) {
  /*If the staff users username needs to be changed*/
  if (isset($_POST['new_staff_username']) && !empty($_POST['new_staff_username'])) {
    changeStaffEntry($_POST['staff_id'],$_POST['new_staff_username'],'Username');
  }
  /*If the staff users password needs to be changed*/
  if (isset($_POST['new_staff_password']) && !empty($_POST['new_staff_password'])) {
    changeStaffPassword($_POST['staff_id'], $_POST['new_staff_password']);
  }
  /*If the staff users forename needs to be changed*/
  if (isset($_POST['new_staff_forename']) && !empty($_POST['new_staff_forename'])) {
    changeStaffEntry($_POST['staff_id'],$_POST['new_staff_forename'],'Forename');
  }
  /*If the staff users surname needs to be changed*/
  if (isset($_POST['new_staff_surname']) && !empty($_POST['new_staff_surname'])) {
    changeStaffEntry($_POST['staff_id'],$_POST['new_staff_surname'],'Surname');
  }
  /*If the staff users email needs to be changed*/
  if (isset($_POST['new_staff_email']) && !empty($_POST['new_staff_email'])) {
    changeStaffEntry($_POST['staff_id'],$_POST['new_staff_email'],'Email');
  }
  /*If the staff users access level needs to be changed*/
  if (isset($_POST['new_staff_access_level']) && !empty($_POST['new_staff_access_level'])) {
    changeStaffEntry($_POST['staff_id'],$_POST['new_staff_access_level'],'AccessLevel');
  }
}


/*Used to remove users from the database*/
function removeStaff($staffID) {
  /*SQL query used to change the location popularity stored on the table*/
  $query = "DELETE FROM `Staff` WHERE `Staff`.`StaffID` = ?";
  /*Connects to the database*/
  $con = databaseConnect();
  /*turns the query into a statement*/
  $stmt = $con->prepare($query);
  $stmt->bind_param("s", $staffID);
  /*Executes the statement code*/
  $stmt->execute();
  /*Disconnects from the database*/
  $con->close();
  /*Stores the active sidebar section to a variable so that when the page is refreshed it redirects the user to the section they had selected*/
  session_start();
  $_SESSION['activeSection'] = 'm11';
  $_SESSION['activeButton'] = 's11';
  /*Prevents form resubmission using a javascript function*/
  echo "<script>if(window.history.replaceState){window.history.replaceState(null, null, window.location.href);}</script>";
}
/*Detects if a location needs to be removed and executes the corresponding function*/
if (isset($_POST['remove_staff'])) {
  removeStaff($_POST['staff_id']);
}


/*Used to add users to the database*/
function addUser($forename, $surname, $email, $gender, $roomNumber) {
  /*Data formatting:*/
  /*Generates the users initials using their forename and surname*/
  $initials = strtoupper($forename[0].$surname[0]);
  /*Makes the forname and surname uppercase before being added to the database*/
  $forename = ucwords($forename);
  $surname = ucwords($surname);
  /*If the variable is an empty string, make it NULL*/
  if ($email === "") { unset($email); }
  if ($roomNumber === "") { unset($roomNumber); }

  /*SQL query used to change the location popularity stored on the table*/
  $query = "INSERT INTO `Users` (`UserID`, `Forename`, `Surname`, `Email`, `Gender`, `RoomNumber`, `Initials`) VALUES (NULL, ?, ?, ?, ?, ?, ?)";
  /*Connects to the database*/
  $con = databaseConnect();
  /*turns the query into a statement*/
  $stmt = $con->prepare($query);
  $stmt->bind_param("ssssss", $forename, $surname, $email, $gender, $roomNumber, $initials);
  /*Executes the statement code*/
  $stmt->execute();
  /*Disconnects from the database*/
  $con->close();
  /*Stores the active sidebar section to a variable so that when the page is refreshed it redirects the user to the section they had selected*/
  session_start();
  $_SESSION['activeSection'] = 'm21';
  $_SESSION['activeButton'] = 's21';
  /*Prevents form resubmission using a javascript function*/
  echo "<script>if(window.history.replaceState){window.history.replaceState(null, null, window.location.href);}</script>";
}
/* Detects if a user is being added and runs the code */
if (isset($_POST['add_user'])) {
  addUser($_POST['user_forename'],$_POST['user_surname'],$_POST['user_email'],$_POST['user_gender'],$_POST['user_room_num']);
}


/*Used to update a users initials*/
function updateUserInitials($userID) {
  /*Sets the initials to the first character of the users first and last names*/
  $query = "UPDATE Users SET Users.Initials=CONCAT(LEFT(Users.Forename, 1), LEFT(Users.Surname, 1))";
  /*Connects to the database*/
  $con = databaseConnect();
  /*turns the query into a statement*/
  $stmt = $con->prepare($query);
  /*Executes the statement code*/
  $stmt->execute();
  /*Disconnects from the database*/
  $con->close();
}
/*Used to change a users database entries*/
/*userID -> the id of the user being edited*/
/*newValue -> the value replacing the old one*/
/*fieldName -> the column where the data is being replaced*/
function changeUserEntry($userID, $newValue, $fieldName) {
  /*SQL query(s) used to change the location popularity stored on the table*/
  if ($fieldName === "Forename") {
    $query = "UPDATE Users SET Forename=? WHERE userID=?";
    $newValue = ucwords($newValue);
  }
  elseif ($fieldName === "Surname") {
    $query = "UPDATE Users SET Surname=? WHERE userID=?";
    $newValue = ucwords($newValue);
  }
  elseif ($fieldName === "Email") {
    $query = "UPDATE Users SET Email=? WHERE userID=?";
  }
  elseif ($fieldName === "RoomNumber") {
    $query = "UPDATE Users SET RoomNumber=? WHERE userID=?";
  }
  elseif ($fieldName === "Gender") {
    $query = "UPDATE Users SET Gender=? WHERE userID=?";
  }
  /*Connects to the database*/
  $con = databaseConnect();
  /*turns the query into a statement*/
  $stmt = $con->prepare($query);
  $stmt->bind_param("ss", $newValue, $userID);
  /*Executes the statement code*/
  $stmt->execute();
  /*Disconnects from the database*/
  $con->close();
  /*Stores the active sidebar section to a variable so that when the page is refreshed it redirects the user to the section they had selected*/
  session_start();
  $_SESSION['activeSection'] = 'm21';
  $_SESSION['activeButton'] = 's21';
  /*Prevents form resubmission using a javascript function*/
  echo "<script>if(window.history.replaceState){window.history.replaceState(null, null, window.location.href);}</script>";
}
/* Detects if the edit location form has been submitted */
if (isset($_POST['edit_user_form'])) {
  /*If the users forename needs to be changed*/
  if (isset($_POST['new_user_forename']) && !empty($_POST['new_user_forename'])) {
    changeUserEntry($_POST['user_id'],$_POST['new_user_forename'],'Forename');
  }
  /*If the users surname needs to be changed*/
  if (isset($_POST['new_user_surname']) && !empty($_POST['new_user_surname'])) {
    changeUserEntry($_POST['user_id'],$_POST['new_user_surname'],'Surname');
  }
  /*If the users email needs to be changed*/
  if (isset($_POST['new_user_email']) && !empty($_POST['new_user_email'])) {
    changeUserEntry($_POST['user_id'],$_POST['new_user_email'],'Email');
  }
  /*If the users gender needs to be changed*/
  if (isset($_POST['new_user_gender']) && !empty($_POST['new_user_gender'])) {
    changeUserEntry($_POST['user_id'],$_POST['new_user_gender'],'Gender');
  }
  /*If the users room number needs to be changed*/
  if (isset($_POST['new_user_room_num']) && !empty($_POST['new_user_room_num'])) {
    changeUserEntry($_POST['user_id'],$_POST['new_user_room_num'],'RoomNumber');
  }
  /*Updates the initials in case the user forename or surname have been changed*/
  updateUserInitials($_POST['user_id']);
}


/*Used to remove users from the database*/
function removeUser($userID) {
  /*SQL query used to change the location popularity stored on the table*/
  $query = "DELETE FROM `Users` WHERE `Users`.`UserID` = ?";
  /*Connects to the database*/
  $con = databaseConnect();
  /*turns the query into a statement*/
  $stmt = $con->prepare($query);
  $stmt->bind_param("s", $userID);
  /*Executes the statement code*/
  $stmt->execute();
  /*Disconnects from the database*/
  $con->close();
  /*Stores the active sidebar section to a variable so that when the page is refreshed it redirects the user to the section they had selected*/
  session_start();
  $_SESSION['activeSection'] = 'm21';
  $_SESSION['activeButton'] = 's21';
  /*Prevents form resubmission using a javascript function*/
  echo "<script>if(window.history.replaceState){window.history.replaceState(null, null, window.location.href);}</script>";
}
/*Detects if a location needs to be removed and executes the corresponding function*/
if (isset($_POST['remove_user'])) {
  removeUser($_POST['user_id']);
}


/*Used to add locations to the database*/
function addLocation($name, $alias, $description, $popularity) {
  /*SQL query used to change the location popularity stored on the table*/
  $query = "INSERT INTO Locations (LocationID, LocationName, LocationAlias, Description, Popularity) VALUES (NULL, ?, ?, ?, ?)";
  /*Connects to the database*/
  $con = databaseConnect();
  /*turns the query into a statement*/
  $stmt = $con->prepare($query);
  $stmt->bind_param("ssss", $name, $alias, $description, $popularity);
  /*Executes the statement code*/
  $stmt->execute();
  /*Disconnects from the database*/
  $con->close();
  /*Stores the active sidebar section to a variable so that when the page is refreshed it redirects the user to the section they had selected*/
  session_start();
  $_SESSION['activeSection'] = 'm31';
  $_SESSION['activeButton'] = 's31';
  /*Prevents form resubmission using a javascript function*/
  echo "<script>if(window.history.replaceState){window.history.replaceState(null, null, window.location.href);}</script>";
}
/* Detects if a location is being added and runs the code */
if (isset($_POST['add_location'])) {
  addLocation($_POST['location_name'],$_POST['location_alias'],$_POST['location_desc'],$_POST['location_pop']);
}


/*Used to change location entrys on the database*/
function changeLocationEntry($locationID, $newValue, $fieldName) {
  /*SQL query(s) used to change the location popularity stored on the table*/
  if ($fieldName === "LocationName") {
    $query = "UPDATE Locations SET LocationName=? WHERE LocationID=?";
  }
  elseif ($fieldName === "LocationAlias") {
    $query = "UPDATE Locations SET LocationAlias=? WHERE LocationID=?";
  }
  elseif ($fieldName === "Description") {
    $query = "UPDATE Locations SET Description=? WHERE LocationID=?";
  }
  elseif ($fieldName === "Popularity") {
    $query = "UPDATE Locations SET Popularity=? WHERE LocationID=?";
  }
  /*Connects to the database*/
  $con = databaseConnect();
  /*turns the query into a statement*/
  $stmt = $con->prepare($query);
  $stmt->bind_param("ss", ucwords($newValue), $locationID);
  /*Executes the statement code*/
  $stmt->execute();
  /*Disconnects from the database*/
  $con->close();
  /*Stores the active sidebar section to a variable so that when the page is refreshed it redirects the user to the section they had selected*/
  session_start();
  $_SESSION['activeSection'] = 'm31';
  $_SESSION['activeButton'] = 's31';
  /*Prevents form resubmission using a javascript function*/
  echo "<script>if(window.history.replaceState){window.history.replaceState(null, null, window.location.href);}</script>";
}
/*Used to change the name of a location*/
if (isset($_POST['edit_location_form'])) {
  /*If the name needs to be changed*/
  if (isset($_POST['new_location_name']) && !empty($_POST['new_location_name'])) {
    changeLocationEntry($_POST['location_id'],$_POST['new_location_name'],'LocationName');
  }
  /*If the alias needs to be changed*/
  if (isset($_POST['new_location_alias']) && !empty($_POST['new_location_alias'])) {
    changeLocationEntry($_POST['location_id'],$_POST['new_location_alias'],'LocationAlias');
  }
  /*If the description needs to change*/
  if (isset($_POST['new_location_desc']) && !empty($_POST['new_location_desc'])) {
    changeLocationEntry($_POST['location_id'],$_POST['new_location_desc'],'Description');
  }
  /*If the popularity needs to be changed*/
  if (isset($_POST['new_location_pop']) && !empty($_POST['new_location_pop'])) {
    changeLocationEntry($_POST['location_id'],$_POST['new_location_pop'],'Popularity');
  }
}


/*Used to remove locations from the database*/
function removeLocation($locationID) {
  /*SQL query used to change the location popularity stored on the table*/
  $query = "DELETE FROM `Locations` WHERE `Locations`.`LocationID` = ?";
  /*Connects to the database*/
  $con = databaseConnect();
  /*turns the query into a statement*/
  $stmt = $con->prepare($query);
  $stmt->bind_param("s", $locationID);
  /*Executes the statement code*/
  $stmt->execute();
  /*Disconnects from the database*/
  $con->close();
  /*Stores the active sidebar section to a variable so that when the page is refreshed it redirects the user to the section they had selected*/
  session_start();
  $_SESSION['activeSection'] = 'm31';
  $_SESSION['activeButton'] = 's31';
  /*Prevents form resubmission using a javascript function*/
  echo "<script>if(window.history.replaceState){window.history.replaceState(null, null, window.location.href);}</script>";
}
/*Detects if a location needs to be removed and executes the corresponding function*/
if (isset($_POST['remove_location'])) {
  removeLocation($_POST['location_id']);
}


/* Run the admin logout function if the admin user logs out*/
/* A form on the admin page is used to POST a variable when the logout button is pressed;
   This code executes when that variable has been set*/
if (isset($_POST['admin_sign_out'])) {
  /*Logs the admin user out*/
  if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
  }
  echo '<script type="text/javascript">window.location.href = "home_page.php";</script>';
  exit();
}

/*Executed when the page is first loaded*/
onPageLoad();
?>
