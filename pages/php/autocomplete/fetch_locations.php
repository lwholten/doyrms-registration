<?php
// This file is used to fetch an array of locations from the database which share a similar name to the 'term' input
// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');

// If the search term is not empty
if ($_POST['term'] != "") {

  // SQL
  // SQL query used to fetch suggested locations
  $query = "SELECT `LocationName` FROM `Locations` WHERE LocationName LIKE CONCAT('%', ?, '%') OR KeyWords LIKE CONCAT('%', ?, '%') LIMIT 0,3";
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // turns the query into a statement
  $stmt = $con->prepare($query);
  // Inserts the term into the query
  $stmt->bind_param("ss", $_POST['term'], $_POST['term']);
  // Executes the statement code
  $stmt->execute();
  $result = $stmt->get_result();
  // Disconnects from the database
  $con->close();

  // Outputs the locations found in an array to be used client side with Ajax
  $locations = [];
  while($record = $result -> fetch_array(MYSQLI_NUM)) {
    foreach ($record as $value) {
      array_push($locations, $value);
    }
  }
  echo json_encode($locations);
}
?>
