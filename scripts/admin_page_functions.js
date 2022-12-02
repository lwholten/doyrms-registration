/*Used to format the edit section to suit the record that is being edited*/

/*section_id -> the id of the section being formatted*/
/*storage_class -> the class of the elements used to store the id of the record being edited */
/*title_content -> content added to the section title*/
function formatEditSection(section_id, storage_class, title_content, record_id=0) {
  /*Assigns the section being formated to a variable*/
  section = document.getElementById(section_id);

  /*Assigns the edit section's title to a variable*/
  section_title = section.getElementsByTagName('h3')[0];
  /*Concatinates the additional content to the edit section title*/
  section_title.innerHTML = 'Edit - '+'\''+title_content+'\'';

  /*Sets the location id for all forms to that of the selected record*/
  const record_id_storage_array = document.getElementsByClassName(storage_class);
  for (var i = 0; i < record_id_storage_array.length; i++) {
    record_id_storage_array[i].setAttribute('value', record_id);
  };

  /*Shows the edit section to the user*/
  toggleHiddenSection(section_id);
}

// Used to disable inputs
function disableInput(id) {
  document.getElementById(id).style.textDecoration = 'line-through';
  document.getElementById(id).querySelector('input').style.backgroundColor = '#DDDDDD';
  document.getElementById(id).querySelector('input').disabled = true;
}
// Used to enable inputs
function enableInput(id) {
  document.getElementById(id).style.textDecoration = 'none';
  document.getElementById(id).querySelector('input').style.backgroundColor = '#FFFFFF';
  document.getElementById(id).querySelector('input').disabled = false;
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
