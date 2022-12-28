// This file is used to keep all page data up to date by refreshing tables and sections at regular intervals
$(document).ready(function () {
  // Recent activity table
  function populateRecentActivityTable() {
      $.ajax({
          url: '../php/tables/staff/recent_activity.php',
          type: 'POST',
          dataType: 'json',
          success: function(response) {
              $('#recent_activity_table').html(response);
          }
      });
    }
  // Users table
  function populateUserStatusTable() {
      $.ajax({
          url: '../php/tables/staff/user_status.php',
          type: 'POST',
          dataType: 'json',
          success: function(response) {
              $('#user_status_table').html(response);
          }
      });
    }
  // Away table
  function populateAwayUsersTable() {
      $.ajax({
          url: '../php/tables/staff/away_users.php',
          type: 'POST',
          dataType: 'json',
          success: function(response) {
              $('#away_users_table').html(response);
          }
      });
    }
  // Restricted table
  function populateRestrictedUsersTable() {
      $.ajax({
          url: '../php/tables/staff/restricted_users.php',
          type: 'POST',
          dataType: 'json',
          success: function(response) {
              $('#restricted_users_table').html(response);
          }
      });
    }
  // Populate all tables
  function populateAllTables() {
    populateRecentActivityTable();
    populateUserStatusTable();
    populateAwayUsersTable();
    populateRestrictedUsersTable();
  }

  // Fetches a two-dimensional array containing the tableID and eventID for every event in the database
  function fetchEventTableIDs() {
      var eventTableIDs;
      $.ajax({
          url: '../php/tables/staff/fetch_event_table_ids.php',
          type: 'POST',
          dataType: 'json',
          // Not asynchronous since we require the response for use outisde the function
          async: false,
          success: function(response) {
                eventTableIDs = response;
          }
      });
    return eventTableIDs;
  }
  // Populates an event table
  // Populates table with ID: tableID with data from event with ID: eventID
  function populateEventTable(tableID, eventID) {
      $.ajax({
          url: '../php/tables/staff/event_table.php',
          type: 'POST',
          dataType: 'json',
          data: ({eventID: eventID}),
          success: function(response) {
              $(''.concat('#',tableID)).html(response);
          }
      });
  }
  // Populates all event tables
  // Fetches all event tables as an array, iterates through the array and populates each table
  function populateAllEventTables() {
    const tables = fetchEventTableIDs();
    for (let i=0; i<tables.length; i++) {
      populateEventTable(tables[i][0], tables[i][1]);
    }
  }

  // Used to load the event sections when the page loads
  function loadEventSections() {
    $.ajax({
        url: '../php/sections/staff/event_sections.php',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            // Once the required HTML code is aquired, add it to the page
            $('#event_sections').html(response);
            // Then populate all the event tables
            populateAllEventTables()
        }
    });
  }

  // Dashboard Analytics
  function updateDashboardAnalytics() {
    $.ajax({
        url: '../php/sections/staff/dashboard_analytics.php',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            // Once the analytics HTML code has been recieved, add it to the page
            $('#dashboard_analytics').html(response);
        }
    });
  }

  // When the page is loaded
  // Event loop to trigger a refresh at regular intervals (all code contained is executed every n milliseconds)
  var intervalId = window.setInterval(function(){
    updateDashboardAnalytics();
    populateAllTables();
    populateAllEventTables();
  }, 5000);

  // Loads all the event sections when the page is first loaded
  updateDashboardAnalytics();
  // Populates each table when the page is first loaded
  populateAllTables();
  // Loads all the event sections when the page is first loaded
  loadEventSections();

});
