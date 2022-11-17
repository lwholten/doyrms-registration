function toggleSidebar() {
  toggle = document.getElementById('sidebar_toggle');
  sidebar = document.getElementById('sidebar');
  sidebar_content = document.getElementById('sidebar_contents');

  if (toggle.checked) {
    sidebar_content.style.display = 'flex';
    sidebar.style.background = 'rgba(55,71,79,0.8)';
    sidebar.style.width = '300px';
    sidebar_content.style.display = 'flex';

    setTimeout(function(){
      sidebar_content.style.opacity = '1.0';
    }, 200);
  }
  else {
    sidebar_content.style.display = 'flex';
    sidebar.style.background = 'rgba(0,0,0,0.3)';
    sidebar.style.width = '80px';
    sidebar_content.style.opacity = 0.0;
    setTimeout(function(){
      sidebar_content.style.display = 'none';
    }, 200);
  }
}

function toggleMainSection(section_id) {
  var new_section = document.getElementById(section_id);
  /*If the parameters are left empty*/
  if (new_section === null) {
    /*Outputs an error message to the console*/
    console.log('ERROR: Could not display new section as the ID was invalid or left empty')
  }
  else {
    /*Saves active sections to an array*/
    var active_sections = document.getElementsByClassName('main_section_active');
    /*Selects the first element of the array*/
    var old_section = active_sections[0];
    /*Hides the element*/
    old_section.style.display = 'none';
    /*Removes the active class from the old section*/
    old_section.classList.remove('main_section_active');

    /*uses 'section_id' to display the new section*/
    new_section.style.display = 'block';
    /*Adds the active class to the new section*/
    new_section.classList.add('main_section_active')
  }
}
