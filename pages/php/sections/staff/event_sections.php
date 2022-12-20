<?php
function populateTable($eventID) {
  /*Connects to the database*/
  $con = new mysqli('localhost', 'dreg_user', 'epq', 'dregDB');
  /*SQL code to get the table data*/
  /*Note, this code MUST be changed in the future not to use a PHP variable in the query statement*/
  $sql = "SELECT Users.UserID, Users.Forename, Users.Surname, (CASE WHEN UserID IN (SELECT DISTINCT UserID FROM Log WHERE EventID=$eventID) THEN (SELECT Log.MinutesLate FROM Log WHERE Log.UserID=Users.UserID AND Log.EventID IS NOT NULL AND CAST(Log.LogTime AS DATE)=CURRENT_DATE ORDER BY LogID DESC LIMIT 1) ELSE NULL END) FROM Users ORDER BY (CASE WHEN UserID IN (SELECT DISTINCT UserID FROM Log WHERE EventID=$eventID) THEN (SELECT Log.MinutesLate FROM Log WHERE Log.UserID=Users.UserID AND Log.EventID IS NOT NULL AND CAST(Log.LogTime AS DATE)=CURRENT_DATE ORDER BY LogID DESC LIMIT 1) ELSE NULL END), Users.Forename ASC;";
  /*Saves the result of the SQL code to a variable*/
  $result = $con->query($sql);
  /*Disconnects from the database*/
  $con->close();
  /*Iterates through the table records and displays them on the web page's table*/
  while($record = $result -> fetch_array(MYSQLI_NUM)) {
    echo "<tr>";
    // Outputs the nature of the event sign out (e.g. absent, late, early, on time)
    // Did not attend the event
    echo "<td><span class='inline-dot ";
    if ($record[3] === NULL) {
      echo "red'></span>Absent";
    }
    // Was late to the event
    elseif (intval($record[3]) < 0) {
      echo "orange'></span>Late";
    }
    // Was on time to the event
    elseif (intval($record[3]) === 0) {
      echo "green'></span>On Time";
    }
    // Was early to the event
    elseif (intval($record[3]) > 0) {
      echo "blue'></span>Early";
    }
    // It is unknown and an error may have occured
    else {
      echo "red'></span>Unknown";
    }
    echo "</td>";
    // Outputs the first and last names of the user
    echo "<td> $record[1] </td>";
    echo "<td> $record[2] </td>";
    // Outputs the timing for the event (how many minutes late/early)
    echo "<td>";
    if (intval($record[3]) > 0) {
      $mins = strval(abs(intval($record[3])));
      echo "$mins Minutes Early";
    }
    elseif (intval($record[3]) < 0) {
      $mins = strval(abs(intval($record[3])));
      echo "$mins minutes late";
    }
    elseif ($record[3] === NULL || intval($record[3]) === 0) {
      echo "-";
    }
    else {
      echo "N/A";
    }
    echo "</td>";
    echo "</tr>";
  }
}
function loadEventSections() {
  // Connects to the database
  $con = new mysqli('localhost', 'dreg_user', 'epq', 'dregDB');
  // SQL code to get all the event data (maximum of 25 events)
  $sql = "SELECT EventID, Event, StartTime FROM Events";
  // Saves the result of the SQL code to a variable
  $result = $con->query($sql);
  // Disconnects from the database
  $con->close();

  // Creates an array containing all of the background gradients
  // Indexing this array gived the gradient that corresponds to the time of the index + 1 (e.g. for 12PM, array[13])
  $backgrounds = [
    'linear-gradient(#012459 0%, #001322 100%)',
    'linear-gradient(#012459 0%, #001323 100%)',
    'linear-gradient(#003972 0%, #001322 100%)',
    'linear-gradient(#004372 0%, #00182b 100%)',
    'linear-gradient(#004372 0%, #011d34 100%)',
    'linear-gradient(#016792 0%, #00182b 100%)',
    'linear-gradient(#07729f 0%, #042c47 100%)',
    'linear-gradient(#12a1c0 0%, #07506e 100%)',
    'linear-gradient(#74d4cc 0%, #1386a6 100%)',
    'linear-gradient(#efeebc 0%, #61d0cf 100%)',
    'linear-gradient(#fee154 0%, #a3dec6 100%)',
    'linear-gradient(#fdc352 0%, #e8ed92 100%)',
    'linear-gradient(#ffac6f 0%, #ffe467 100%)',
    'linear-gradient(#fda65a 0%, #ffe467 100%)',
    'linear-gradient(#fd9e58 0%, #ffe467 100%)',
    'linear-gradient(#f18448 0%, #ffd364 100%)',
    'linear-gradient(#f06b7e 0%, #f9a856 100%)',
    'linear-gradient(#ca5a92 0%, #f4896b 100%)',
    'linear-gradient(#5b2c83 0%, #d1628b 100%)',
    'linear-gradient(#371a79 0%, #713684 100%)',
    'linear-gradient(#28166b 0%, #45217c 100%)',
    'linear-gradient(#192861 0%, #372074 100%)',
    'linear-gradient(#040b3c 0%, #233072 100%)',
    'linear-gradient(#040b3c 0%, #012459 100%)',
  ];
  // For each event from the database, echo a section containing its data
  while($record = $result -> fetch_array(MYSQLI_NUM)) {
    // Gets the starting hour of the event and stores it as an integer (e.g. 12 PM as 12, 4PM as 16 etc.)
    $hour = intval(substr($record[2], 0, 2));
    // Sets the table background to the background corresponding to the events starting time
    $tableBackground = $backgrounds[$hour];

    // The starting tag for this section
    $section_id = strtolower("$record[1]_event_section");
    echo "<section class='main_section' id='$section_id'>";
    // The section title
    $title = ucwords(strtolower($record[1]));
    echo "<h2>Events - $title</h2>";
    // The section description
    $description = "Here you can see all users who have signed out for the event, $record[1]. This includes the nature of the sign out, whether the user was early, late or on time";
    echo "<p>$description</p><spacer></spacer>";
    // Declares the start of a table
    $table_id = strtolower("$record[1]_event_table");
    echo "<div class='table_wrapper event_table_wrapper' style='background: $tableBackground'><header><h2>$title</h2></header><table class='table' id='$table_id'><tr><th>Nature</th><th>Forename</th><th>Surname</th><th>Timing</th></tr>";
    // Populates the table with data
    populateTable($record[0]);
    // Declares the end of the table and section
    echo "</table><footer/ class='event_footer'></div></section>";
  }
}
?>
