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
 <style> 
 .b_title{color:#000000;font-family:"Trebuchet MS", Arial;font-size:1.4em;}
 .b_content{color:#000000;font-family:"Trebuchet MS", Arial;font-size:1em;}
 table{width:95%;margin-bottom:10px;margin-topm:10px;border:0;border-collapse:collapse;font-family:"Trebuchet MS", Arial;color:#ffffff;text-align:center;font-size:1em;}
 th{background-color:rgba(30,30,30,0.2);padding-top:6px;padding-bottom:6px;border:0;}
 tr:nth-child(odd){background-color:rgba(80,80,80,0.2);}
 tr:nth-child(even){background-color:rgba(40,40,40,0.2);}
 td{padding-top:4px;padding-bottom:4px;padding-left:10px;padding-right:10px;border:0;}
 .title{width:40%;font-size:0.8em;}
 .subject{width:5%;font-size:0.8em;}
 .author{width:5%;font-size:0.8em;}
 .ptime{width:20%;font-size:0.8em;}
 .lastpost{width:20%;font-size:0.8em;}
 </style>
 <body onload = "init()"> 
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
     	<div id = "side_sec">
     		<button onclick = "test(this)" class = "side_switch" id= "1">Threads</button> <br>
     		<button onclick = "test(this)" class = "side_switch" id= "2">OnMap</button><br>
     		<button onclick = "test(this)" class = "side_switch" id= "3">Friends</button> <br>
     		<button onclick = "test(this)" class = "side_switch" id= "4">Block</button>
     	</div>
     	<div id = "thread_area">
     		<div id = "d1" class = "content">
     			<script type="text/javascript">
     				var xmlhttp = new XMLHttpRequest();

         	        xmlhttp.open("POST", "DB_operations.php", false);
         	        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
         	        xmlhttp.send("function=checkThread");
         	        //alert(xmlhttp.responseText);
                 	var content = xmlhttp.responseXML.getElementsByTagName("thread");
                 	if(content.length <= 0){
                 		document.write('<p class = "b_content">Oops! No threads for you. </p>');
                 	}
                 	else{
                        document.write('<table align="center" style="text-align:center;" border="1">');
                        document.write('<tbody><tr><th>TITLE</th><th>SUB</th><th>USER</th><th>TIME</th><th>LASTPOST</th></tr>');
                        for(var i=0; i<content.length; i++)
                        {
                        	document.write("<tr><td class='title'>");
                        	document.write('<a href="http://localhost:8080/Project%202/thread.php?thread=');
                        	document.write(content[i].getAttribute('tid'));
                        	document.write('">');
                            document.write(content[i].getAttribute('title'));
                            document.write('</a>');
                            document.write("</td><td class='subject'>");
                            document.write(content[i].getAttribute('content'));
                            document.write("</td><td class='author'>");
                            document.write(content[i].getAttribute('author'));
                            document.write("</td><td class='ptime'>");
                            document.write(content[i].getAttribute('ptime'));
                            document.write("</td><td class='lastpost'>");
                            document.write(content[i].getAttribute('lastpost'));
                            document.write("</td></tr>");
                        }
                        document.write("</tbody></table>");
                 	}
     			</script>
     		</div>
     		<div id = "d2" class = "content">
     			<!-- this area is for the mapview -->
     			<div id = "map"></div>
     			<script>
                	function initMap() {
                    var map = new google.maps.Map(document.getElementById('map'), {
                      zoom: 10,
                      center: {lat: 40.731, lng: -73.997}
                    });
                  }
                </script>
                <script async defer
                 src="https://maps.googleapis.com/maps/api/js?key=xxx">
                </script>
     		</div>
     		<div id = "d3" class = "content">
     			<script type="text/javascript">
     				var xmlhttp = new XMLHttpRequest();
     				//alert("aaa");
         	        //sychronize mode
         	        xmlhttp.open("POST", "DB_operations.php", false);
         	        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
         	        xmlhttp.send("function=checkFriend");
         	        //alert(xmlhttp.responseText);
                 	document.write('<h2 class = "b_title">Your current friends:</h2>');
                 	//alert("aaa");
                 	var content = xmlhttp.responseXML.getElementsByTagName("friend");
                 	if(content.length <= 0){
                 		document.write('<p class = "b_content">It\'s empty!. </p>');
                 	}
                 	else{
                        document.write('<table align="center" style="text-align:center;" border="1">');
                        document.write('<tbody><tr><th>UID</th><th>NAME</th><th>EMAIL</th></tr>');
                        for(var i=0; i<content.length; i++)
                        {
                        	document.write("<tr><td>");
                            document.write(content[i].getAttribute('uid'));
                            document.write("</td><td>");
                            document.write(content[i].getAttribute('uname'));
                            document.write("</td><td>");
                            document.write(content[i].getAttribute('email'));
                            document.write("</td></tr>");
                        }
                        document.write("</tbody></table>");
                 	}
                 	
                 	document.write('<h2 class = "b_title">Your unapproved friendship:</h2>');
                 	var content = xmlhttp.responseXML.getElementsByTagName("unapprove");
                 	if(content.length <= 0){
                 		document.write('<p class = "b_content">It\'s empty!. </p>');
                 	}
                 	else{
                        document.write('<table align="center" style="text-align:center;" border="1">');
                        document.write('<tbody><tr><th>UID</th><th>NAME</th><th>EMAIL</th><th>CANCEL</th></tr>');
                        for(var i=0; i<content.length; i++)
                        {
                        	document.write("<tr><td>");
                            document.write(content[i].getAttribute('uid'));
                            document.write("</td><td>");
                            document.write(content[i].getAttribute('uname'));
                            document.write("</td><td>");
                            document.write(content[i].getAttribute('email'));
                            document.write("</td><td>");
                            document.write("<button onclick = 'cancelFApp(this)' value =" + content[i].getAttribute('uid') + ">cancel</button>");
             	            document.write("</td></tr>");
                        }
                        document.write("</tbody></table>");
                 	}

                 	document.write('<h2 class = "b_title">Friendship for you to approve:</h2>');
                 	var content = xmlhttp.responseXML.getElementsByTagName("toapprove");
                 	if(content.length <= 0){
                 		document.write('<p class = "b_content">It\'s empty!. </p>');
                 	}
                 	else{
                        document.write('<table align="center" style="text-align:center;" border="1">');
                        document.write('<tbody><tr><th>UID</th><th>NAME</th><th>EMAIL</th><th>APPROVE</th></tr>');
                        for(var i=0; i<content.length; i++)
                        {
                        	document.write("<tr><td>");
                            document.write(content[i].getAttribute('uid'));
                            document.write("</td><td>");
                            document.write(content[i].getAttribute('uname'));
                            document.write("</td><td>");
                            document.write(content[i].getAttribute('email'));
                            document.write("</td><td>");
                            document.write("<button onclick = 'approveFriend(this)' value =" + content[i].getAttribute('uid') + ">approvel</button>");
             	            document.write("</td></tr>");
                        }
                        document.write("</tbody></table>");
                 	}

                 	document.write('<h2 class = "b_title">Another users in your block:</h2>');
                 	var content = xmlhttp.responseXML.getElementsByTagName("canapprove");
                 	if(content.length <= 0){
                 		document.write('<p class = "b_content">It\'s empty!. </p>');
                 	}
                 	else{
                        document.write('<table align="center" style="text-align:center;" border="1">');
                        document.write('<tbody><tr><th>UID</th><th>NAME</th><th>EMAIL</th><th>APPLY</th></tr>');
                        for(var i=0; i<content.length; i++)
                        {
                        	document.write("<tr><td>");
                            document.write(content[i].getAttribute('uid'));
                            document.write("</td><td>");
                            document.write(content[i].getAttribute('uname'));
                            document.write("</td><td>");
                            document.write(content[i].getAttribute('email'));
                            document.write("</td><td>");
                            document.write("<button onclick = 'applyFriend(this)' value =" + content[i].getAttribute('uid') + ">apply</button>");
             	            document.write("</td></tr>");
                        }
                        document.write("</tbody></table>");
                 	}
     			</script>
     		</div>
     		<div id = "d4" class = "content">
     			<script type="text/javascript">
     				// check whether the user has join a block, then construct the table.
     				var xmlhttp = new XMLHttpRequest();
     				//alert("aaa");
         	        //sychronize mode
         	        xmlhttp.open("POST", "DB_operations.php", false);
         	        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
         	        xmlhttp.send("function=checkUserBlock");
        	        //extract header info
        	        var hInfo = xmlhttp.responseXML.getElementsByTagName("blockInfo");
        	        var ib = hInfo[0].getAttribute('ib');
        	        var blockName = hInfo[0].getAttribute('bname');
        	        var blockID = hInfo[0].getAttribute('b_id');
         	        //not yet approved
         	        if(ib == "NOT"){
             	        document.write('<h2 class = "b_title">waiting for approvement</h2>');
             	        document.write('<p class = "b_content">you have applied for: ');
             	        document.write(blockName);
             	        document.write("</p>");
             	        document.write('<button onclick = "cancelApp(this)" class = "block_button" id= "ca">Cancel this application</button> <br>');
                    }
                    //approved or not yet applied
         	        else
         	        {
             	        if(ib == 'y')
             	        {
                 	        document.write('<h2 class = "b_title">you are in a block now</h2>');
                 	        document.write('<p class = "b_content">your block is: ');
                 	        document.write(blockName);
                 	        document.write("</p>");
                 	        //alert("aaa");
                 	        var content = xmlhttp.responseXML.getElementsByTagName("user");
                 	        if(content.length <= 0){
                 	        	document.write('<p class = "b_content">No one applying now. </p>');
                 	        }
                 	        else{
                     	        document.write('<p class = "b_content">other users applying for this block: </p>');
                     	        document.write('<table align="center" style="text-align:center;" border="1">');
                     	        document.write('<tbody><tr><th>UID</th><th>NAME</th><th>APPROVE</th></tr>');
                     	        var uid = "";
                     	        for(var i=0; i<content.length; i++)
                     	        {
                         	        uid = content[i].getAttribute('uid');
                     	        	document.write("<tr><td>");
                     	            document.write(uid);
                     	            document.write("</td><td>");
                     	            document.write(content[i].getAttribute('uname'));
                     	            document.write("</td><td>");
                     	            document.write('<button onclick = "approve(this)" value =' + uid + '>approve</button>');
                     	            document.write("</td></tr>");
                     	        }
                     	        document.write("</tbody></table>");
                 	        }
                 	        document.write('<button onclick = "cancelApp()" class = "block_button" id= "qb">Quit this Block</button> <br>');
             	        }
             	        else
             	        {
             	        	document.write('<h2 class = "b_title">you have not apply for any block</h2>');
                 	        document.write("<p class = 'b_content'>here's the blocks you can apply:</p>" );
                 	        document.write('<table align="center" style="text-align:center;" border="1">');
                 	        document.write('<tbody><tr><th>ID</th><th>NAME</th><th>APPLY</th></tr>');
                 	        var content = xmlhttp.responseXML.getElementsByTagName("block");
                 	        var b_id = "";
                 	        for(var i=0; i<content.length; i++)
                 	        {
                     	        b_id = content[i].getAttribute('b_id');
                 	        	document.write("<tr><td>");
                 	            document.write(b_id);
                 	            document.write("</td><td>");
                 	            document.write(content[i].getAttribute('bname'));
                 	            document.write("</td><td>");
                 	            document.write("<button onclick = 'apply(this)' value =" + b_id + ">apply</button>");
                 	            document.write("</td></tr>");
                 	        }
                 	        document.write("</tbody></table>");
             	        }
         	        }
     			</script>
     		</div>
     	</div>
 	</div>
 	<script>
 		function toUserInfo()
 		{
 			window.location.replace("personal.php");
 		}
 		function test(element)
 		{
 			
 	 		//first hide every div
 	 		var divs = document.getElementsByClassName("content");
 	 		for(var i=0; i<divs.length; i++)
 	 		{
 	 	 		divs[i].style.display = 'none';
 	 		}
 	 		var target_div = document.getElementById('d' + element.id);
 	 		target_div.style.display = '';
 		}
 		function init()
 		{
 			var divs = document.getElementsByClassName("content")
 	 		for(var i=1; i<divs.length; i++)
 	 		{
 	 	 		divs[i].style.display = 'none';
 	 		}
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
 		function cancelApp()
 		{
 			var xmlhttp = new XMLHttpRequest();
 	        //sychronize mode
 	        xmlhttp.open("POST", "DB_operations.php", false);
 	        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
 	        xmlhttp.send("function=cancelApp&uid=" + '<?php echo $_SESSION["uid"]; ?>');
 	        if(xmlhttp.responseText!="SUCCESS")
 	 	        alert(xmlhttp.responseText);
 	        document.location.reload();
 		}
 		function approve(user)
 		{
 			var xmlhttp = new XMLHttpRequest();
 	        //sychronize mode
 	        xmlhttp.open("POST", "DB_operations.php", false);
 	        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
 	        xmlhttp.send("function=approveApp&uid=" + '<?php echo $_SESSION["uid"]; ?>' + "&applicant=" + user.value);
 	        if(xmlhttp.responseText!="SUCCESS")
 	 	        alert(xmlhttp.responseText);
 	        document.location.reload();
 		}
 		function cancelFApp(friend)
 		{
 			var xmlhttp = new XMLHttpRequest();
 	        //sychronize mode
 	        xmlhttp.open("POST", "DB_operations.php", false);
 	        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
 	        xmlhttp.send("function=cancelFA&uid=" + '<?php echo $_SESSION["uid"]; ?>' + "&target=" + friend.value);
 	        if(xmlhttp.responseText!="SUCCESS")
 	 	        alert(xmlhttp.responseText);
 	        document.location.reload();
 		}
 		function approveFriend(friend)
 		{
 			var xmlhttp = new XMLHttpRequest();
 	        //sychronize mode
 	        xmlhttp.open("POST", "DB_operations.php", false);
 	        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
 	        xmlhttp.send("function=approveFA&uid=" + '<?php echo $_SESSION["uid"]; ?>' + "&target=" + friend.value);
 	        if(xmlhttp.responseText!="SUCCESS")
 	 	        alert(xmlhttp.responseText);
 	        document.location.reload();
 		}
 		function applyFriend(friend)
 		{
 			var xmlhttp = new XMLHttpRequest();
 	        //sychronize mode
 	        xmlhttp.open("POST", "DB_operations.php", false);
 	        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
 	        xmlhttp.send("function=applyF&uid=" + '<?php echo $_SESSION["uid"]; ?>' + "&target=" + friend.value);
 	        if(xmlhttp.responseText!="SUCCESS")
 	 	        alert(xmlhttp.responseText);
 	        document.location.reload();
 		}
 		function apply(block)
 		{
 			var xmlhttp = new XMLHttpRequest();
 	        //sychronize mode
 	        xmlhttp.open("POST", "DB_operations.php", false);
 	        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
 	        xmlhttp.send("function=applyBlock&b_id=" + block.value + "&uid=" + '<?php echo $_SESSION["uid"]; ?>');
 	        if(xmlhttp.responseText!="SUCCESS")
 	 	        alert(xmlhttp.responseText);
	 	    document.location.reload();
 		}
    </script>
 </body>
</html>

