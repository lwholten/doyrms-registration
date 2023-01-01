<?php
// This file is used to fetch an array of events from the database which share a similar name to the 'term' input
// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');

// If the search term is not empty
if ($_POST['term'] != "") {

  // Variables
  if ($_POST['nature'] === 'signin') {
    // Query to select sign in events
    $query = "SELECT `Event` FROM `Events` WHERE Event LIKE CONCAT('%', ?, '%') AND SignInEvent=1 LIMIT 0,3";
  }
  elseif ($_POST['nature'] === 'signout') {
    // Query to select sign out events
    $query = "SELECT `Event` FROM `Events` WHERE Event LIKE CONCAT('%', ?, '%') AND SignInEvent=0 LIMIT 0,3";
  }
  elseif ($_POST['nature'] === 'both' ) {
    // Query to select both sign in and out events
    $query = "SELECT `Event` FROM `Events` WHERE Event LIKE CONCAT('%', ?, '%') LIMIT 0,3";
  }
  else {
    // Query to select both sign in and out events
    $query = "SELECT `Event` FROM `Events` WHERE Event LIKE CONCAT('%', ?, '%') LIMIT 0,3";
  }

  // SQL
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // turns the query into a statement
  $stmt = $con->prepare($query);
  // Inserts the term into the query
  $stmt->bind_param("s", $_POST['term']);
  // Executes the statement code
  $stmt->execute();
  $result = $stmt->get_result();
  // Disconnects from the database
  $con->close();

  // Outputs the events found in an array to be used client side with Ajax
  $locations = [];
  while($record = $result -> fetch_array(MYSQLI_NUM)) {
    foreach ($record as $value) {
      array_push($locations, $value);
    }
  }
  echo json_encode($locations);
}
?>
