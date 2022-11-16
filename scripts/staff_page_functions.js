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
    sidebar.style.width = '100px';
    sidebar_content.style.opacity = 0.0;
    setTimeout(function(){
      sidebar_content.style.display = 'none';
    }, 200);
  }
}
