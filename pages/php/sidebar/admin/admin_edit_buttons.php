<?php
// This file contains code that displays the admin buttons and subtitls in the staff apge sidebar

// Makes this file only executable if the signed in staff user has a high enough access level
if ($_SESSION['staffAccessLevel'] >= 3) {

    // Variable to store HTML
    $sidebarHTML = '';
    // Subtitle
    $sidebarHTML .= "<li class='sidebar_subtitle'><h3>Admin</h3></li>";
    // Buttons
    $sidebarHTML .= <<<BUTTONS
    <li onclick="toggleMainSection(this.id,'edit_staff_section')" class="sidebar_button" id="edit_staff_button"><h4>Edit Staff</h4></li>
    <li onclick="toggleMainSection(this.id,'edit_users_section')" class="sidebar_button" id="edit_users_button"><h4>Edit Users</h4></li>
    <li onclick="toggleMainSection(this.id,'edit_locations_section')" class="sidebar_button" id="edit_locations_button"><h4>Edit Locations</h4></li>
    <li onclick="toggleMainSection(this.id,'edit_events_section')" class="sidebar_button" id="edit_events_button"><h4>Edit Events</h4></li> 
    BUTTONS;

    // Outputs HTML
    echo $sidebarHTML;

}
?>
