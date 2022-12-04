<?php
/*Initiates a session when the page is first loaded*/
session_start();
/*Includes all files used in the main page*/
include("../../css/mainstyles.css");
include("../../css/main_page.css");
include("../html/home_page.html");

// Used to connect to the database
function databaseConnect() {
  // Create connection
  $con = new mysqli('localhost', 'dreg_user', 'epq', 'dregDB');

  // Check connection
  if ($con->connect_error) {
    echo "<script>notification('Something went wrong, please try again later','error',3000)</script>";
    echo "<script>console.error('The system could not connect to the database!')</script>";
    return 0;
  }
  return $con;
}

// Functions used for the staff login process
function checkStaffExists($username) {
  // Used to check whether a staff user exists within the database
  $query = "SELECT CASE WHEN EXISTS (SELECT * FROM Staff WHERE Username=?) THEN 1 ELSE 0 END";
  // Connects to the database
  $con = databaseConnect();
  // Prepares and executes the statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("s", $username);
  $stmt->execute();
  // Binds the result to a variable
  $stmt->bind_result($result);
  $stmt->fetch();
  // Disconnects from the database
  $con->close();

  // Note that an integer is returned, not a string
  return $result;
}
function checkStaffPassword($username, $password) {
  // Used to verify  associated with the staff username
  $query = "SELECT Salt, Hash FROM Staff WHERE Username=?";
  // Connects to the database
  $con = databaseConnect();
  // Prepares and executes the statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("s", $username);
  $stmt->execute();
  // Binds the result to their respective variables
  $stmt->bind_result($result['salt'], $result['hash']);
  $stmt->fetch();
  // Disconnects from the database
  $con->close();

  // Concatinates the accounts salt to the start of the password
  $saltedPassword = $result['salt'].$password;
  // Verifies the password and returns a boolean value
  if (password_verify($saltedPassword, $result['hash'])) {
      unset($result);
      return 1;
  } else {
      unset($result);
      return 0;
  }
}
function fetchStaffDetails($username) {
  // used to get the access level and id associated with a staff user
  $query = "SELECT Staff.StaffID, Staff.AccessLevel FROM Staff WHERE Username=? LIMIT 1";
  // Connects to the database
  $con = databaseConnect();
  // Prepares and executes the statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("s", $username);
  $stmt->execute();
  // Binds the results to a variable
  $stmt->bind_result($result['staffID'], $result['staffAccessLevel']);
  $stmt->fetch();
  // Disconnects from the database
  $con->close();

  // Note that this function returns an INTEGER, NOT a STRING
  return $result;
}
function completePreviousStaffLog($staffID) {
  // This function is used to complete all previous sign in logs for a staff user

  // SQL query used to complete the log
  $query = "UPDATE StaffLog SET Complete=1 WHERE StaffID=? AND SignOutTime IS NULL AND Complete=0 ORDER BY SignInTime DESC";
  // Connects to the database
  $con = databaseConnect();
  // turns the query into a statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("i", $staffID);
  // Executes the statement code
  $stmt->execute();
  // Disconnects from the database
  $con->close();
}
function logStaffSignIn($staffID) {
  // This function is used to save a log to the database for a given staff user

  // SQL query used to create the log
  $query = "INSERT INTO StaffLog ( StaffLogID, StaffID, SignInTime, SignOutTime, Complete ) VALUES (NULL, ?, CURRENT_TIMESTAMP, NULL, 0)";
  // Connects to the database
  $con = databaseConnect();
  // turns the query into a statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("i", $staffID);
  // Executes the statement code
  $stmt->execute();
  // Disconnects from the database
  $con->close();
}
// Executes if a staff login is detected
if (isset($_POST['staff_login_form'])) {

  // Checks if the staff user exists
  $exists = checkStaffExists($_POST['staff_username']);
  // If they exist
  if ($exists === 1) {
    unset($exists);
    $correct = checkStaffPassword($_POST['staff_username'], $_POST['staff_password']);
    // If the password is correct
    if ($correct === 1) {
      unset($correct);
      // initiates a PHP session and binds the username
      if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
      }
      $_SESSION['staffUsername'] = $_POST['staff_username'];
      // Gets the staff users ID and access level from the database
      $staffDetails = fetchStaffDetails($_POST['staff_username']);
      // Binds the staff details to the session
      $_SESSION['staffID'] = $staffDetails['staffID'];
      $_SESSION['staffAccessLevel'] = $staffDetails['staffAccessLevel'];
      // Sets the home section to be the active section
      $_SESSION["activeSection"] = 'm1';
      $_SESSION["activeButton"] = 's1';
      // Updates a previous log entry for this staff user if it exists
      completePreviousStaffLog($staffDetails['staffID']);
      // Sets the logged in state to true and logs the user in
      logStaffSignIn($staffDetails['staffID']);
      $_SESSION['loggedIn'] = 1;

      // Redirects the staff user to the correct page
      if ($staffDetails['staffAccessLevel'] >= 3) {
        // Admin page
        echo '<script type="text/javascript">window.location.href = "admin_page.php";</script>';
      }
      elseif ($staffDetails['staffAccessLevel'] >= 1) {
        // Staff page
        echo '<script type="text/javascript">window.location.href = "staff_page.php";</script>';
      }
    }
    // If the password is incorrect
    else {
      unset($correct);
      echo "<script>notification('The username or password you entered were incorrect','validation',2000)</script>";
      exit();
    }
  }
  // If they do not exist
  else {
    unset($exists);
    echo "<script>notification('The username or password you entered were incorrect','validation',2000)</script>";
    exit();
  }
}

// Functions used for the user sign out process
function checkLocationExists($location) {
  // Used to check whether a location exists within the database
  $query = "SELECT CASE WHEN EXISTS (SELECT * FROM Locations WHERE Locations.LocationName=?) THEN 1 ELSE 0 END";
  // Connects to the database
  $con = databaseConnect();
  // Prepares and executes the statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("s", $location);
  $stmt->execute();
  // Binds the result to a variable
  $stmt->bind_result($result);
  $stmt->fetch();
  // Disconnects from the database
  $con->close();

  return $result;
}
function checkUserExists($name) {
  // Splits the name input into first and last names using a temporary array
  $tempArray = explode(" ", $name);
  $fname = $tempArray[0];
  $lname = $tempArray[1];
  unset($tempArray);

  // Used to check whether a user exists in the database
  $query = "SELECT CASE WHEN EXISTS (SELECT * FROM Users WHERE Users.Forename=? AND Users.Surname=?) THEN 1 ELSE 0 END";
  // Connects to the database
  $con = databaseConnect();
  // Prepares and executes the statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("ss", $fname, $lname);
  $stmt->execute();
  // Binds the result to a variable
  $stmt->bind_result($result);
  $stmt->fetch();
  // Disconnects from the database
  $con->close();

  return $result;
}
function checkUserLogExists($name) {
  // This function is used to check if there is an active user log
  // This can be used to infer whether a user is currently signed in or out

  // Splits the name input into first and last names using a temporary array
  $tempArray = explode(" ", $name);
  $fname = $tempArray[0];
  $lname = $tempArray[1];
  unset($tempArray);
  // Used to check whether a the user has an uncompleted log
  $query = "SELECT CASE WHEN EXISTS (SELECT * FROM Log INNER JOIN Users ON Log.UserID = Users.UserID WHERE Users.Forename=? AND Users.Surname=? AND Log.Complete=0) THEN 1 ELSE 0 END;";
  // Connects to the database
  $con = databaseConnect();
  // Prepares and executes the statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("ss", $fname, $lname);
  $stmt->execute();
  // Binds the result to a variable
  $stmt->bind_result($result);
  $stmt->fetch();
  // Disconnects from the database
  $con->close();

  // Note, Returns 1 IF there is an uncompleted log (user is signed out)
  // 0 -> NO LOG
  // NOTe, Returns 0 IF there are no uncompleted logs (user is signed in)
  // 1 -> LOG EXISTS
  return $result;
}
function completePreviousUserLog($name) {
  // This function is used to complete all previous logs for a user
  // This is useful for when a user forgets to sign back in after having signed out previously
  // As it will set their previous sign in time to NULL and complete the log

  // Splits the name input into first and last names using a temporary array
  $tempArray = explode(" ", $name);
  $fname = $tempArray[0];
  $lname = $tempArray[1];
  unset($tempArray);
  // SQL query used to complete the log
  $query = "UPDATE Log SET Complete=1 WHERE UserID IN ( SELECT UserID FROM Users WHERE Forename=? AND Surname=? ) AND TimeIn IS NULL AND Complete=0 ORDER BY TimeOut DESC";
  // Connects to the database
  $con = databaseConnect();
  // turns the query into a statement
  $stmt = $con->prepare($query);
  $stmt->bind_param("ss", $fname, $lname);
  // Executes the statement code
  $stmt->execute();
  // Disconnects from the database
  $con->close();
}
function createUserLog($name, $location) {
  // This function is used to save a sign out log to the database for a given user
  // It sets the sign out time to the current time and sets the sign in time to NULL
  // A field in the log specifies whether the user has signed in since signing out (Complete)
  // And this field is used to determing completed logs

  // Splits the name input into first and last names using a temporary array
  $tempArray = explode(" ", $name);
  $fname = $tempArray[0];
  $lname = $tempArray[1];
  unset($tempArray);
  // SQL query used to create the log
  $logQuery = "INSERT INTO Log ( UserID, LocationID, TimeOut, TimeIn, EventID, Complete ) SELECT  Users.UserID, Locations.LocationID, CURRENT_TIMESTAMP, NULL, NULL, 0 FROM Users, Locations WHERE Users.Forename=? AND Users.Surname=? AND Locations.LocationName=?";
  // SQL query used to update the users location
  $locationQuery = "UPDATE Users SET LastActive=CURRENT_TIMESTAMP, LocationID=(SELECT LocationID FROM Locations WHERE LocationName=?) WHERE Forename=? AND Surname=?";
  // Connects to the database
  $con = databaseConnect();
  // turns the log query into a statement
  $logStmt = $con->prepare($logQuery);
  $logStmt->bind_param("sss", $fname, $lname, $location);
  // Executes the statement code
  $logStmt->execute();
  // turns the location query into a statement
  $locationStmt = $con->prepare($locationQuery);
  $locationStmt->bind_param("sss", $location, $fname, $lname);
  // Executes the statement code
  $locationStmt->execute();
  // Disconnects from the database
  $con->close();
  return true;
}
function updateUserLog($name) {
  // This function updates the most recent log for a user
  // It is executed when a user signs back in to the system
  // When executed, it sets the sign in time to the current time and completes the log

  // Splits the name input into first and last names using a temporary array
  $tempArray = explode(" ", $name);
  $fname = $tempArray[0];
  $lname = $tempArray[1];
  unset($tempArray);
  // SQL query used to create the log
  $logQuery = "UPDATE Log SET TimeIn=CURRENT_TIME, Complete=1 WHERE UserID IN ( SELECT UserID FROM Users WHERE Forename=? AND Surname=? ) AND TimeIn IS NULL AND Complete=0 ORDER BY TimeOut DESC LIMIT 1";
  // SQL query used to update the users location
  $locationQuery = "UPDATE Users SET LastActive=CURRENT_TIMESTAMP, LocationID=NULL WHERE Forename=? AND Surname=?";
  // Connects to the database
  $con = databaseConnect();
  // turns the log query into a statement
  $logStmt = $con->prepare($logQuery);
  $logStmt->bind_param("ss", $fname, $lname);
  // Executes the statement code
  $logStmt->execute();
  // turns the location query into a statement
  $locationStmt = $con->prepare($locationQuery);
  $locationStmt->bind_param("ss", $fname, $lname);
  // Executes the statement code
  $locationStmt->execute();
  // Disconnects from the database
  $con->close();
  return true;
}
// Executes if a user sign out is detected
if (isset($_POST['user_sign_out'])) {
  // If the user and location exist within the database
  $userExists = checkUserExists($_POST['sign_out_initials_field']);
  $locationExists = checkLocationExists($_POST['sign_out_locations_field']);
  // If they exist
  if ($userExists === 1 && $locationExists === 1) {
    unset($userExists, $locationExists);
    // Complete the users previous log(s)
    // Note that if the user signs in after signing out, all their logs would be completed anyway
    // This is merely a measure to ensure that logs are completed in the event a user forgets to sign back in
    completePreviousUserLog($_POST['sign_out_initials_field']);
    // Creates a new log for this user, since all previous logs have been completed
    $success = createUserLog($_POST['sign_out_initials_field'], $_POST['sign_out_locations_field']);
    // If this was successful, it will relay the message to the user
    if ($success) {
      echo "<script>notification('You have been signed out successfully!','success',2000)</script>";
      unset($success);
    }
    // If this was unsuccessful, it will relay the message to the user and output an error
    else {
      echo "<script>notification('Something went wrong, please try again later','validation',3000)</script>";
      echo "<script>console.error('The system could not sign a user out: The function \'CreateUserLog()\' was unsuccessful!')</script>";
      unset($success);
    }
  }
  // If the location exists but not the user
  elseif ($userExists === 0 && $locationExists === 1) {
    unset($userExists, $locationExists);
    echo "<script>notification('Please make sure the name you entered is valid','validation',2000)</script>";
    exit();
  }
  // If the user exists but not the location
  elseif ($userExists === 1 && $locationExists === 0) {
    unset($userExists, $locationExists);
    echo "<script>notification('Please make sure the location you selected is valid','validation',2000)</script>";
    exit();
  }
  // If neither the location nor user exists
  else {
    unset($userExists, $locationExists);
    echo "<script>notification('Please make sure the name and location you entered are valid','validation',2000)</script>";
    exit();
  }
}
// Executes if a user sign in is detected
if (isset($_POST['user_sign_in'])) {
  // If the user exists in the database
  $userExists = checkUserExists($_POST['sign_in_initials_field']);
  // If they exist
  if ($userExists === 1) {
    unset($userExists);
    // Checks whether the user is already signed in by searching for an uncompleted log
    // If there are no uncompleted logs, it can be assumed that the user is signed in
    $userSignedIn = checkUserLogExists($_POST['sign_in_initials_field']);
    if ($userSignedIn === 1) {
      unset($userSignedIn);
      // Updates the previous log created, in this case it was created when the user signed out
      $success = updateUserLog($_POST['sign_in_initials_field']);
      // If this was successful, it will relay the message to the user
      if ($success) {
        echo "<script>notification('You have been signed in successfully!','success',2000)</script>";
        unset($success);
      }
      // If this was unsuccessful, it will relay the message to the user and output an error
      else {
        echo "<script>notification('Something went wrong, please try again later','error',3000)</script>";
        echo "<script>console.error('The system could not sign a user out: The function \'UpdateUserLog()\' was unsuccessful!')</script>";
        unset($success);
      }
    }
    elseif ($userSignedIn === 0) {
      unset($userSignedIn);
      echo "<script>notification('It looks like you are already signed in!','warning',2000)</script>";
      exit();
    }
    else {
      unset($userSignedIn);
      echo "<script>notification('Sorry, we can't sign you in at the moment, please try again later.','error',3000)</script>";
      exit();
    }
  }
  else {
    unset($userExists);
    echo "<script>notification('Please make sure the name you entered is valid','validation',2000)</script>";
    exit();
  }
}
?>
