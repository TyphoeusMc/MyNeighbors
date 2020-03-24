<?php
session_start();
if (isset($_SESSION["admin"]) && $_SESSION["admin"] === true) {
    ;
} else {
    $_SESSION["admin"] = false;
    echo "<script>";
    echo "window.location.replace='mainpage.php'";
    echo "</script>";
}
?>
<!DOCTYPE html>
<html>
 <head> 
  <link rel = "stylesheet" href = "user_main.css">
  <meta charset="UTF-8" /> 
  <title>MYNEIGHBORS</title>
 </head> 
 <body onload = "initPage()"> 
 	<div id = "page_header">
 		<img alt="MyNeighbors" src="resources/LOGO.png" id = "logo">
 		<form id = "search_area" name = "search_area" action="result_page.php">
 			<input id = "search_input_blank" type = "text" name="search_input" />
 			<select id = "search_types_sec">
 				<option value = "threads/messages"> threads/messages </option>
 				<option value = "friends"> friends </option>
 			</select>
 			<input class = "header_button" id= "search_button" type="submit" value="GO" />
 		</form>
 		<input class = "header_button" id= "profile_button" type="button" value='<?php echo $_SESSION["uname"]; ?>' onclick = "toUserInfo()"/>
 		<input class = "header_button" id= "new_thread_button" type="button" value="+" onclick="addNewThread()" />
 		<input class = "header_button" id= "log_out" type="button" value="log out" onclick="userQuit()" />
 	</div>
 	<div id = "main_body">
 		<div id = "title">
 			<h3 id = "title_content">TITLE</h3>
 		</div>
 		<div id = "content">
 			<p id = "content">CONTENT</p>
 		</div>
 	</div>
 	<script type="text/javascript">
 	function initPage()
 	{
 		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.open("POST", "DB_operations.php", false);
	    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	    xmlhttp.send("function=getThreadInfo&thread=" + getVariable("thread"));
	 	//alert(xmlhttp.responseText);
	 	var content = xmlhttp.responseXML.getElementsByTagName("thread");
	    document.getElementById('title_content').innerHTML = content[0].getAttribute('title');
 	   	document.getElementById('content').innerHTML = content[0].getAttribute('content');
 	}
 	function toUserInfo()
    {
	    window.location.replace("personal.php");
    }
 	function userQuit()
    {
	    var xmlhttp = new XMLHttpRequest();
	    //sychronize mode
	    xmlhttp.open("POST", "login_validate.php", false);
	    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	    xmlhttp.send("function=quitSession");
	    window.location.replace("mainpage.php");
    }
    function addNewThread()
    {
	    window.location.replace("newThread.php");
    }
    function getVariable(variable)
    {
           var query = window.location.search.substring(1);
           var vars = query.split("&");
           for (var i=0;i<vars.length;i++) {
                   var pair = vars[i].split("=");
                   if(pair[0] == variable){
                       return pair[1];
                   }
           }
           return(false);
    }
    
 	</script>
 </body>
</html>

