<?php session_start();
$_SESSION["admin"] = null;
?>
<!DOCTYPE html>
<html>
 <head> 
  <link rel = "stylesheet" href = "main_page_style.css">
  <meta charset="UTF-8" /> 
  <title>MYNEIGHBORS</title> 
 </head> 
 <body> 
  <div id = title_area>
   <img src="resources/LOGO.png" id = "logo" alt="HOUSE">
  </div>
  <div class = "front_page">
  <p id = "subtitle" >login, explore around</p>
  <div id = "input_btn" >
   <form name="login" method="post" action="user_main.php" onsubmit="return check_input();"> 
    <p class = "description" > e-mail* </p>
    <input class = "input_blank" type = "text" name="email_input" />
    <p class = "description" > password* </p>
    <input class = "input_blank" type = "password" name="password" />
    <input id = "button_log" type="submit" value="LOGIN" />
   </form> 
   <form name="register" method="post" action="register.php"> 
    <p id = description2>Don't have an account?</p>
    <input id = "button_reg" type="submit" value="SIGN UP" />
   </form> 
  </div>
  </div>
  <script type="text/javascript">
      function check_input(){
    	  if(login.email_input.value == ""){
    		  alert("please input your email");
    		  login.email_input.focus();
    		  return false;
    	  }
    	  if(login.password.value == ""){
    		  alert("please input your password");
    		  login.password.focus();
    		  return false;
    	  }
    	  // next step is to check password
          var xmlhttp = new XMLHttpRequest();
          //sychronize mode
          xmlhttp.open("POST", "login_validate.php", false);
          xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
          xmlhttp.send("function=login_validate&email=" + login.email_input.value + "&password=" + login.password.value);
          if(xmlhttp.responseText == "NULL"){
              alert("this email is not registered.");
              return false;
          }
          else if(xmlhttp.responseText == "FAIL"){
        	  alert("password invalid");
              return false;
          }
    	  return true;
      }

  </script>
 </body>
</html>






