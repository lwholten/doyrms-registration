function toggleHiddenSection(new_section_ID) {
  /*Gets an array of all edit section*/
  const edit_sections = document.getElementsByClassName('hidden_section');
  /*Gets the new section using its ID*/
  const new_section = document.getElementById(new_section_ID);

  /*Iterates the array of edit sections*/
  for (const edit_section of edit_sections) {
    /*If the section is active, make it inactive and breaks the loop*/
    if (edit_section.classList.contains('hidden_section_active')) {
      edit_section.classList.remove('hidden_section_active');
      break
    }
    /*Otherwise it will continue until it finds the active section*/
    else {
      continue
    }
  }
  /*Makes the new section active*/
  new_section.classList.add('hidden_section_active');
}
