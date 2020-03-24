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
  <link rel = "stylesheet" href = "new_thread.css">
  <meta charset="UTF-8" /> 
  <title>MYNEIGHBORS</title>
 </head> 
 <body onload = "init()"> 
  <div class = "register_page">
  <p id = "register" >NEW THREAD</p>
  <form name="sign_up" method="post" action = "user_main.php" onsubmit="return check_input();">
   <div id = "name_area">
    <p class = "description" >title*:</p>
    <input class = "input_blank" type = "text" id = "name_input" name="name_input" />
   </div>
   <div id = subject_area>
   	<p class = "description" > subject: </p>
    <input class = "input_blank" type = "text" id = "subject" name="subject" list = "subjects"/>
    <datalist id="subjects"></datalist>
   </div>
   <div id = target_area>
   	<p class = "description" > target: </p>
   	<select class = "input_blank" name = "target">
 		<option value = "hood"> hood </option>
 		<option value = "block"> block </option>
 		<option value = "friend"> friend </option>
 		<option value = "neighbor"> neighbor </option>
 	</select>
   </div>
   <div id = "password_area">
    <p class = "description" >content*:</p>
    <textarea id = "profile_input" name="profile_input"> </textarea>
   </div>
   
   <div id="map"></div>
   <script>
     var markers = [];
     //var state = "";
     //var street_number = "";
     //var route = "";
     var street_number = '<?php echo $_SESSION["building"]; ?>';
     var route = '<?php echo $_SESSION["street"]; ?>';
     //var city = "";
     var address = '<?php echo $_SESSION["address"]; ?>';
     var user_lat = '<?php echo $_SESSION["lat"]; ?>';
     var user_lng = '<?php echo $_SESSION["lng"]; ?>';
     var pin_location = "";
   	 function initMap() {
       var map = new google.maps.Map(document.getElementById('map'), {
         zoom: 14,
         center: {lat: parseFloat(user_lat), lng: parseFloat(user_lng)}
       });
       var marker = new google.maps.Marker({
           position: {lat: parseFloat(user_lat), lng: parseFloat(user_lng)},
           map: map
       });
       markers.push(marker);
       var geocoder = new google.maps.Geocoder;
       var infowindow = new google.maps.InfoWindow;

       map.addListener('click', function(event) {
    	 deleteMarkers();
    	 pin_location = event.latLng;
         geocodeLatLng(geocoder, map, infowindow, event.latLng);
       });
     }
     function geocodeLatLng(geocoder, map, infowindow, locations) {
       geocoder.geocode({'location': locations}, function(results, status) {
         if (status === 'OK') {
           if (results[0]) {
             var marker = new google.maps.Marker({
               position: locations,
               map: map
             });
             markers.push(marker);
             //distract components of the address
             for (var i = 0; i < results[0].address_components.length; i++) {
                 if (results[0].address_components[i].types[0] == "street_number") {
                   street_number = results[0].address_components[i].long_name;
               	 }
                 else if (results[0].address_components[i].types[0] == "route") {
                   route = results[0].address_components[i].long_name;
                 }
             }
             address = results[0].formatted_address;
             infowindow.setContent(results[0].formatted_address);
             for (var i = 0; i < markers.length; i++) {
                 infowindow.open(map, markers[i]);
             }
           } else {
             window.alert('No results found');
           }
         } else {
           window.alert('Geocoder failed due to: ' + status);
         }
       });
     }
     function setMapOnAll(map) {
         for (var i = 0; i < markers.length; i++) {
             markers[i].setMap(map);
         }
     }
     function deleteMarkers() {
         setMapOnAll(null);
         markers = [];
     }
   </script>
   <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCwu7KKXLKCDT73mZsYUEge83Hzz12KrWM&callback=initMap">
   </script>
   <input class = "button_reg" type="submit" value="POST IT!" />
   </form> 
   <form name="return" method="post" action = "user_main.php">
   	<input class = "button_reg" type="submit" value="RETURN" />
   </form>
  </div>
  <script type="text/javascript">
     function init(){
    	var xmlhttp = new XMLHttpRequest();
     	//sychronize mode
     	xmlhttp.open("POST", "DB_operations.php", false);
     	xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
     	xmlhttp.send("function=getSubjects&hood=" + '<?php echo $_SESSION["hood"]; ?>');
     	//alert(xmlhttp.responseText);
     	if(xmlhttp.responseText != "NULL"){	
     		var dl = document.getElementById("subjects");
     		var content = xmlhttp.responseXML.getElementsByTagName("subject");
  	    	for(var i=0; i<content.length; i++){
  	  	    	var op=document.createElement("option");
  	    		op.setAttribute("value",content[i].getAttribute('content')); 
  	    		dl.appendChild(op);
  	    	}
     	}
     }
  	 function check_input(){
        //first check basic format, like completeness of input
	 	if(sign_up.name_input.value == ""){
	    	alert("please fill any required blank");
	    	sign_up.name_input.focus();
			return false;
		}
	 	else if(sign_up.profile_input.value == ""){
	    	alert("please fill any required blank");
	    	sign_up.profile_input.focus();
			return false;
		}
		//check whether user has choose a place
		else if(address == ""){
			alert("please spot your address");
			return false;
		}	
		//further check database about the email address
		if(pin_location!="")
		{
			user_lat = pin_location.lat();
        	user_lng = pin_location.lng();
		}
	 	var xmlhttp = new XMLHttpRequest();
        xmlhttp.open("POST", "DB_operations.php", false);
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xmlhttp.send("function=postThread&latitude=" + user_lat + "&longitude=" + user_lng + 
                      "&title=" + sign_up.name_input.value + "&subject=" + sign_up.subject.value + 
                      "&content=" + sign_up.profile_input.value + "&target=" + sign_up.target.value + 
                      "&uid=" + '<?php echo $_SESSION["uid"]; ?>' + "&address=" + address + "&hood=" + '<?php echo $_SESSION["hood"]; ?>');
        alert(xmlhttp.responseText);
        if(xmlhttp.responseText == "NOHOOD"){
        	alert("No corresponding hood in your selected area!");
            return false;
        }
        else if(xmlhttp.responseText != "SUCCESS"){
        	alert("Add user failed");
            return false;
        } 
  	    return true;
  	 }
  </script>
 </body>
</html>






