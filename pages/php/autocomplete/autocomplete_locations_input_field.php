<?php
// SQL query used to change the location popularity stored on the table
$query = "SELECT `LocationName` FROM `Locations` WHERE LocationName LIKE ? OR LocationAlias LIKE ? LIMIT 0,3";
// Connects to the database
$con = new mysqli('localhost', 'dreg_user', 'epq', 'dregDB');
// turns the query into a statement
$stmt = $con->prepare($query);

// If the term is not empty, find it
if ($_POST['term'] != "") {
  $term = "%".$_POST['term']."%";
}
else {
  echo "";
}

// Inserts the term into the query
$stmt->bind_param("ss", $term, $term);
// Executes the statement code
$stmt->execute();
$result = $stmt->get_result();
// Disconnects from the database
$con->close();


// Iterates through the table records and displays them on the web page's table
while($record = $result -> fetch_array(MYSQLI_NUM)) {
  foreach ($record as $value) {
    echo "<li onclick='selectLocation(this.childNodes[0].innerHTML)'><h3>$value</h3></li>";
  }
}
?>
