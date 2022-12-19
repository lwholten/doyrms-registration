<?php
function loadEventCards() {
  /*Connects to the database*/
  $con = new mysqli('localhost', 'dreg_user', 'epq', 'dregDB');
  /*SQL code to get all the event data (maximum of 25 events)*/
  $sql = "SELECT Event, LocationName, StartTime, EndTime, Deviation, Days, Alerts FROM Events INNER JOIN Locations ON Events.LocationID = Locations.LocationID UNION SELECT Event, LocationID, StartTime, EndTime, Deviation, Days, Alerts FROM Events WHERE LocationID IS NULL ORDER BY StartTime ASC LIMIT 25";
  /*Saves the result of the SQL code to a variable*/
  $result = $con->query($sql);
  /*Disconnects from the database*/
  $con->close();

  // Creates an array containing all of the background gradients
  // Indexing this array gived the gradient that corresponds to the time of the index + 1 (e.g. for 12PM, array[13])
  $backgrounds = [
    'linear-gradient(rgb(0, 0, 12) 0%, rgb(0, 0, 12) 0%);',
    'linear-gradient(rgb(2, 1, 17) 85%, rgb(25, 22, 33) 100%);',
    'linear-gradient(rgb(2, 1, 17) 60%, rgb(32, 32, 44) 100%);',
    'linear-gradient(rgb(2, 1, 17) 10%, rgb(58, 58, 82) 100%);',
    'linear-gradient(rgb(32, 32, 44) 0%, rgb(81, 81, 117) 100%);',
    'linear-gradient(rgb(64, 64, 92) 0%, rgb(111, 113, 170) 80%, rgb(138, 118, 171) 100%);',
    'linear-gradient(rgb(74, 73, 105) 0%, rgb(112, 114, 171) 50%, rgb(205, 130, 160) 100%);',
    'linear-gradient(rgb(117, 122, 191) 0%, rgb(133, 131, 190) 60%, rgb(234, 176, 209) 100%);',
    'linear-gradient(rgb(130, 173, 219) 0%, rgb(235, 178, 177) 100%);',
    'linear-gradient(rgb(148, 197, 248) 1%, rgb(166, 230, 255) 70%, rgb(177, 181, 234) 100%);',
    'linear-gradient(rgb(183, 234, 255) 0%, rgb(148, 223, 255) 100%);',
    'linear-gradient(rgb(155, 226, 254) 0%, rgb(103, 209, 251) 100%);',
    'linear-gradient(rgb(144, 223, 254) 0%, rgb(56, 163, 209) 100%);',
    'linear-gradient(rgb(87, 193, 235) 0%, rgb(36, 111, 168) 100%);',
    'linear-gradient(rgb(87, 193, 235) 0%, rgb(36, 111, 168) 100%);',
    'linear-gradient(rgb(36, 115, 171) 0%, rgb(30, 82, 142) 70%, rgb(91, 121, 131) 100%);',
    'linear-gradient(rgb(30, 82, 142) 0%, rgb(38, 88, 137) 50%, rgb(157, 166, 113) 100%);',
    'linear-gradient(rgb(30, 82, 142) 0%, rgb(114, 138, 124) 50%, rgb(233, 206, 93) 100%);',
    'linear-gradient(rgb(21, 66, 119) 0%, rgb(87, 110, 113) 30%, rgb(225, 196, 94) 70%, rgb(178, 99, 57) 100%);',
    'linear-gradient(rgb(22, 60, 82) 0%, rgb(79, 79, 71) 30%, rgb(197, 117, 45) 60%, rgb(183, 73, 15) 80%, rgb(47, 17, 7) 100%);',
    'linear-gradient(rgb(7, 27, 38) 0%, rgb(7, 27, 38) 30%, rgb(138, 59, 18) 80%, rgb(36, 14, 3) 100%);',
    'linear-gradient(rgb(1, 10, 16) 30%, rgb(89, 35, 11) 80%, rgb(47, 17, 7) 100%);',
    'linear-gradient(rgb(1, 10, 16) 30%, rgb(89, 35, 11) 80%, rgb(47, 17, 7) 100%);',
    'linear-gradient(rgb(0, 0, 12) 80%, rgb(21, 8, 0) 100%);',
  ];

  /*Iterates through the table records and displays them on the web page's table*/
  while($record = $result -> fetch_array(MYSQLI_NUM)) {
    // Gets the starting hour of the event and stores it as an integer (e.g. 12 PM as 12, 4PM as 16 etc.)
    $hour = intval(substr($record[3], 0, 2));
    // Sets the card background to the background corresponding to this integer
    $cardBackground = $backgrounds[$hour];
    // Creates the 'card' for this event using a list item
    echo "<li style='background: $cardBackground;'><div class='text event_card'>";
      echo "<header style='color: white;'>";
        echo "<h2>$record[0]</h2>";
      echo "</header>";
      echo "<article>";
        // Outputs the location name
        echo "<h3>$record[1]</h3>";
        // Title for the days column
        echo "<h3>Days</h3>";
        // Outputs the times that the event is active
        $start = substr($record[2], 0, 5);
        $end = substr($record[3], 0, 5);
        echo "<div class='content'><h4>Start</h4><h4>End</h4><h3>$start</h3><h3>$end</h3><h4>Notes</h4></div>";
        unset($start, $end);
        // Outputs a list containing every day of the week with a bar based on whether it is enabled or not
        $days = [
          'M' => 'Monday',
          'T' => 'Tuesday',
          'W' => 'Wednesday',
          'R' => 'Thursday',
          'F' => 'Friday',
          'U' => 'Saturday',
          'S' => 'Sunday'
        ];
        echo "<ul class='days'>";
        // For every day, if the event contains the days corresponding letter, display a true
        foreach(str_split('MTWRFUS') as $day){
          echo "</li><h4><span class='inline-bar ";
          if (str_contains($record[5], $day)) {
            echo "green";
          }
          else {
            echo "red";
          };
          // Outputs the day that corresponds to the days letter
          echo "'></span>".$days[$day]."</h4></li>";
        }
        echo "</ul>";
        // Outputs a break to push the alerts to next next column
        if (intval($record[4]) === 0) {
          echo "<h5>Users must be on time</h5>";
        }
        else {
          echo "<h5>Users may $record[4] minutes late/early</h5>";
        }
        // Outputs the alert status
        if ($record[6] === '1') {
          echo "<h4 class='alerts'><span class='inline-bar green'></span>Alerts Enabled</h4>";
        }
        elseif ($record[6] === '0') {
          echo "<h4 class='alerts'><span class='inline-bar red'></span>Alerts Disabled</h4>";
        }
        else {
          echo "<h4 class='alerts'><span class='inline-bar orange'></span>Alerts Unknown</h4>";
        }
      echo "</article>";
    echo "</div></li>";
  }
}
?>
