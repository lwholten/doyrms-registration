<?php
// Config
$ini = parse_ini_file('/var/www/html/doyrms-registration/app.ini');

// Functions
// Used to search through a list of possible actions
function searchActions($term) {
  // Array of all applicable actions
  $actionIndex = [
    ["download data", "#download_data_button"],
    ["sign out", "#sign_out_button"],
    ["sign user out", "#sign_out_user_button"],
    ["sign user in", "#sign_in_user_button"],
    ["restrict user", "#restrict_user_button"],
    ["unrestrict user", "#unrestrict_user_button"],
    ["mark user away", "#away_user_button"],
    ["mark user present", "#present_user_button"],
    ["change password", "#change_password_button"]
  ];
  // A string is used to contain all 'suggestion' list elements
  $suggestions = "";
  // For each menu item, check whether the search term is similar
  for ($i = 0; $i < count($actionIndex); $i++) {
    // If the term is similar to the current menu item, suggest the menu item
    if (stripos($actionIndex[$i][0], $term) !== FALSE) {
      // Function called when the suggested item is clicked
      $function = "triggerButton('".$actionIndex[$i][1]."')";
      // The HTML used to display the list item
      $suggestions .= htmlspecialchars('<li onclick="'.$function.'">'.ucwords($actionIndex[$i][0]).'</li>');
    }
  }
  // Return the suggestions
  return $suggestions;
}
// Used to search through all menu items
function searchMenu($term) {
  // Array of all applicable menu
  $menuIndex = [
    ["home", 'home_button', 'home_section'],
    ["activity", 'activity_button', 'activity_section'],
    ["users", 'users_button', 'users_section'],
    ["restricted", 'restricted_button', 'restricted_section'],
    ["away", "away_button", "away_section"],
    ["summary", "events_summary_button", "events_summary_section"],
    ["events", "events_summary_button", "events_summary_section"],
    ["edit details", "edit_details_button", "edit_details_section"],
    ["customise", "customise_button", "customise_section"]
  ];
  // A string is used to contain all 'suggestion' list elements
  $suggestions = "";
  // For each menu item, check whether the search term is similar
  for ($i = 0; $i < count($menuIndex); $i++) {
    // If the term is similar to the current menu item, suggest the menu item
    if (stripos($menuIndex[$i][0], $term) !== FALSE) {
      // Function called when the suggested item is clicked
      $function = "toggleMainSection('".$menuIndex[$i][1]."', '".$menuIndex[$i][2]."')";
      // The HTML used to display the list item
      $suggestions .= htmlspecialchars('<li onclick="'.$function.'">'.ucwords($menuIndex[$i][0]).'</li>');
    }
  }
  // Return the suggestions
  return $suggestions;
}
// Used to search through all the events
function searchEvents($term) {
  global $ini;
  // SQL to search the database for users
  $query = "SELECT LOWER(REPLACE(Event, ' ', '_')), Event FROM `Events` WHERE Event LIKE CONCAT('%', ?, '%') LIMIT 0, 5";
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // turns the query into a statement
  $stmt = $con->prepare($query);
  // Inserts the term into the query
  $stmt->bind_param("s", $term);
  // Executes the statement code
  $stmt->execute();
  $result = $stmt->get_result();
  // Disconnects from the database
  $con->close();

  // A string is used to contain all 'suggestion' list elements
  $suggestions = "";
  // Iterates through the results and adds each user to a variable
  while($record = $result -> fetch_array(MYSQLI_NUM)) {
    // Determines the section id for this suggestion
    $section_id = strtolower("$record[0]_event_section");
    // Determines the button id for this suggestion
    $button_id = strtolower("$record[0]_event_button");
    // Function called when the suggested item is clicked
    $function = "toggleMainSection('".$button_id."', '".$section_id."')";
    // Creates the HTML required for the suggestion
    $suggestions .= htmlspecialchars('<li onclick="'.$function.'">'.ucwords($record[1]).'</li>');
  }
  // Return the suggestions
  return $suggestions;
}
// Used to search for all users
function searchUsers($term) {
  global $ini;
  // SQL to search the database for users
  $query = "SELECT Users.Forename, Users.Surname, Users.UserID FROM `Users` WHERE CONCAT(Users.Forename, ' ', Users.Surname) LIKE CONCAT('%', ?, '%') ORDER BY Users.Forename LIMIT 0, 5";
  // Connects to the database
  $con = new mysqli($ini['db_hostname'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
  // turns the query into a statement
  $stmt = $con->prepare($query);
  // Inserts the term into the query
  $stmt->bind_param("s", $term);
  // Executes the statement code
  $stmt->execute();
  $result = $stmt->get_result();
  // Disconnects from the database
  $con->close();

  // A string is used to contain all 'suggestion' list elements
  $suggestions = "";
  // Iterates through the results and adds each user to a variable
  while($record = $result -> fetch_array(MYSQLI_NUM)) {
    $suggestions .= htmlspecialchars("<li onclick='displayUserDetails($record[2])'>$record[0] $record[1]</li>");
  }
  // Return the suggestions
  return $suggestions;
}

// Combines all the search functions into one
function search($term) {
  // Note that this is a long string containing every suggestion as a list item element (<li>)
  $searchResults = "";
  // The values are displayed in order of the functions where the top functions' results takes priority
  $searchResults .= searchMenu($term);
  $searchResults .= searchEvents($term);
  $searchResults .= searchActions($term);
  $searchResults .= searchUsers($term);
  // The returned value is encoded using 'htmlspecialchars()' so it will need decoding
  return $searchResults;
}

// If the term is not empty
if ($_POST['term'] != "") {
  // Perform a search and output the result
  echo htmlspecialchars_decode(search($_POST['term']));
}
// If the term is empty output no results
else {
  echo "";
}
?>
