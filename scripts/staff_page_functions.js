function toggleSidebar() {
  toggle = document.getElementById('sidebar_toggle');
  sidebar = document.getElementById('sidebar');
  sidebar_content = document.getElementById('sidebar_contents');
  container = document.getElementById('sidebar_contents_container')

  if (toggle.checked) {
    sidebar_content.style.display = 'flex';
    sidebar.style.width = '300px';
    container.style.overflowY = 'auto';
    setTimeout(function(){
      sidebar_content.style.opacity = '1.0';
          sidebar.style.minWidth = '300px';
    }, 200);
  }
  else {
    sidebar_content.style.display = 'flex';
    sidebar.style.minWidth = '50px';
    sidebar.style.width = '50px';
    sidebar_content.style.opacity = 0.0;
    container.style.overflowY = 'hidden';
    setTimeout(function(){
      sidebar_content.style.display = 'none';
    }, 200);
  }
}

function toggleMainSection(button_id, section_id) {
  var section = document.getElementById(section_id);
  var button = document.getElementById(button_id);
  /*If the parameters are left empty*/
  if (section === null) {
    /*Outputs an error message to the console*/
    console.log('ERROR: the \'section_id\' is invalid');
  }
  else if (button === null) {
    /*Outputs an error message to the console*/
    console.log('ERROR: the \'button_id\' is invalid');
  }
  else {
    /*Saves active sections and active buttons to an array*/
    var active_sections = document.getElementsByClassName('main_section_active');
    var active_buttons = document.getElementsByClassName('sidebar_button_active');
    /*Selects the first element of the arrays*/
    var old_section = active_sections[0];
    var old_button = active_buttons[0];
    /*Hides the old section and removes the active class*/
    old_section.style.display = 'none';
    old_section.classList.remove('main_section_active');
    /*resets the background color of the old button and removes the active class*/
    old_button.classList.remove('sidebar_button_active');
    /*Shows the new section and adds the active class*/
    section.style.display = 'block';
    section.classList.add('main_section_active');
    /*Updates the background color of the selected button and adds the active class*/
    button.classList.add('sidebar_button_active');
  }
}
