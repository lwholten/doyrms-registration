<!DOCTYPE html>
<head>

<?php include("php/staff_login.php");?>
<?php include("php/student_sign_out.php");?>
<?php include("php/student_sign_in.php");?>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>DOYRMS Registration</title>

</head>

<body>
  <header class="text" id="main_header">
    <img src="img/logo.png"></img>
    <h1>DOYRMS Register</h1>
    <navbar>
      <button class="navbar_button blue_button" id="back_button" onclick="backButton()"><span class="left_arrow"></span><spacer></spacer><h5>BACK</h5></button>
      <button class="navbar_button" id="staff_button" onclick="toggleStaffLoginPreset()"><h5>STAFF LOGIN</h5></button>
      <span class="divider"></span>
      <button onclick="toggleFooter()"><h5>CONTACT</h5></button>
      <span class="divider"></span>
      <button onClick="databaseConnect()"><h5>HELP</h5></button>
    </navbar>
  </header>
  <section class="text" id="login_box">
    <div class="login_preset" id="student_selection_preset">
      <header>
        <h2>Welcome</h2>
      </header>
      <div id="student_selection_grid" class="text">
        <button class="blue_button" onclick="toggleStudentPreset('log_in')"><h1>Sign In</h1></button>
        <divider></divider>
        <button class="blue_button" onclick="toggleStudentPreset('log_out')"><h1>Sign Out</h1></button>
      </div>
    </div>
    <div class="login_preset" id="student_sign_in_preset">
      <header>
        <h2>Sign In</h2>
      </header>
      <form id="student_sign_in_form" class="text" action="" method="POST">
        <label for="student_sign_in_name"><h3>Your Initials</h3></label>
        <input type="text" id="student_sign_in_name" name="student_sign_in_name" placeholder="" value="">

        <input class="blue_button" id="student_sign_in_submit" type="submit" value="Submit">
      </form>
    </div>
    <div class="login_preset" id="student_sign_out_preset">
      <header>
        <h2>Sign Out</h2>
      </header>
      <form id="student_sign_out_form" class="text" action="" method="POST">
        <label for="student_sign_out_name"><h3>Your Initials</h3></label>
        <input type="text" id="student_sign_out_name" name="student_sign_out_name" placeholder="" value="">

        <label for="location"><h3>Location</h3></label>
        <select class="text" id="student_sign_out_location" name="student_sign_out_location" placeholder="" value="">
        </select>

        <input class="blue_button" id="student_sign_out_submit" type="submit" value="Submit">
      </form>
    </div>
    <div class="login_preset" id="staff_login_preset">
      <header>
        <h2>Staff Login</h2>
      </header>
      <form id="staff_login_form" class="text" action="" method="POST">
        <label for="staff_username"><h3>Username</h3></label>
        <input type="text" id="staff_username" name="staff_username" placeholder="" value="">

        <label for="staff_password"><h3>Password</h3></label>
        <input type="password" id="staff_password" name="staff_password" placeholder="" value="">

        <input class="blue_button" id="staff_login_submit" type="submit" value="Submit">
      </form>
    </div>
  </section>
  <footer id="main_footer">
    <div id="footer_collapsed_preset"></div>
    <div id="footer_expanded_preset" class="text">
      <div class="text_grid">
        <h3>Contact Us</h3>
        <h4>Email:</h4>
        <h4 id="contact_email" onclick="copyContactEmail()">luke.holten@doyrms.com <span class="right_arrow"></span></h4>
        <h4>Phone:</h4>
        <h4>0769420699</h4>
      </div>
    </div>
  </footer>
</body>
