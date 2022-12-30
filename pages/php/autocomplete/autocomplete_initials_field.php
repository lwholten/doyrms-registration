<?php
// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');

// If the search term is not empty
if ($_POST['term'] != "") {

  // SQL
  // SQL query used to fetch suggested users
  $query = "SELECT CONCAT('[', Users.Initials, '] ', Users.Forename, ' ', Users.Surname) AS UserData FROM Users WHERE CONCAT('[', Users.Initials, '] ', Users.Forename, ' ', Users.Surname) LIKE CONCAT('%', ?, '%') LIMIT 0,3";
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

  // Outputs the suggested users
  while($record = $result -> fetch_array(MYSQLI_NUM)) {
    foreach ($record as $value) {
      echo "<li onclick='selectInitials(this.childNodes[0].innerHTML)'><h3>$value</h3></li>";
    }
  }

}
?>
