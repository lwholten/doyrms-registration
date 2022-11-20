/*Used to format the edit section to suit the record that is being edited*/

/*section_id -> the id of the section being formatted*/
/*title_content -> content added to the section title*/
function formatEditSection(section_id, title_content, location_id=0) {
  /*Assigns the section being formated to a variable*/
  section = document.getElementById(section_id);

  /*Assigns the edit section's title to a variable*/
  section_title = section.getElementsByTagName('h3')[0];
  /*Concatinates the additional content to the edit section title*/
  section_title.innerHTML = 'Edit - '+'\''+title_content+'\'';

  /*Sets the location id for all forms to that of the selected record*/
  const location_id_storage_array = document.getElementsByClassName('location_id_storage');
  for (var i = 0; i < location_id_storage_array.length; i++) {
    location_id_storage_array[i].setAttribute('value', location_id);
  };

  /*Shows the edit section to the user*/
  toggleHiddenSection(section_id);
}

/*Used to swith hidden sections*/
function toggleHiddenSection(section_id) {
  /*Gets an array of all edit section*/
  const hidden_sections = document.getElementsByClassName('hidden_section');
  /*Gets the new section using its ID*/
  const section = document.getElementById(section_id);

  /*Iterates the array of edit sections*/
  for (const hidden_section of hidden_sections) {
    /*If the section is active, make it inactive and breaks the loop*/
    if (hidden_section.classList.contains('hidden_section_active')) {
      hidden_section.classList.remove('hidden_section_active');
      break
    }
    /*Otherwise it will continue until it finds the active section*/
    else {
      continue
    }
  }
  /*Makes the new section active*/
  section.classList.add('hidden_section_active');
  section.scrollIntoView({ block: 'center',  behavior: 'smooth' });
}
