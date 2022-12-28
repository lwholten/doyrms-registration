<?php
// SQL query used to change the location popularity stored on the table
$query = "SELECT Users.Forename, Users.Surname FROM `Users` WHERE CONCAT(Users.Forename, ' ', Users.Surname) LIKE ? LIMIT 0, 5;";
// Connects to the database
$con = new mysqli('localhost', 'dreg_user', 'epq', 'dregDB');
// turns the query into a statement
$stmt = $con->prepare($query);

// If the term is not empty
if ($_POST['term'] != "") {
  // Concatenates a '%' to the start and end of the term to make the SQL query more effective
  $term = "%".$_POST['term']."%";
}
// If the term is empty, return none
else {
  echo "";
}

// Inserts the term into the query
$stmt->bind_param("s", $term);
// Executes the statement code
$stmt->execute();
$result = $stmt->get_result();
// Disconnects from the database
$con->close();


// Iterates through the table records and displays them on the web page's table
while($record = $result -> fetch_array(MYSQLI_NUM)) {
  echo "<li>$record[0] $record[1]</li>";
}
?>
