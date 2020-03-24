<!DOCTYPE html>
<html>
 <head> 
  <link rel = "stylesheet" href = "register.css">
  <meta charset="UTF-8" /> 
  <title>MYNEIGHBORS</title>
 </head> 
 <body> 
  <div id = title_area>
  <p id = "title" > MyNeighbors </p>
  </div>
  <div class = "register_page">
  <p id = "register" >SIGN UP</p>
  <form name="sign_up" method="post" action="mainpage.php" onsubmit="return check_input();">
   <div id = "email_area">
    <p class = "description" >e-mail*</p>
    <input class = "input_blank" type = "text" name="email_input" />
   </div>
   <div id = "name_area">
    <p class = "description" >name*</p>
    <input class = "input_blank" type = "text" name="name_input" />
   </div>
   <div id = "password_area">
    <p class = "description" > password* </p>
    <input class = "input_blank" type = "password" name="password" />
    <p class = "description" > confirm password* </p>
    <input class = "input_blank" type = "password" name="confirm_password" />
    <p class = "description" > Choose your location in the map below </p>
   </div>
   <div id="map"></div>
   <script>
     var markers = [];
     //var state = "";
     var street_number = "";
     var route = "";
     //var city = "";
     var address = "";
     var pin_location;
   	 function initMap() {
       var map = new google.maps.Map(document.getElementById('map'), {
         zoom: 8,
         center: {lat: 40.731, lng: -73.997}
       });
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
                 /*
                 else if (results[0].address_components[i].types[0] == "political") {
                   city = results[0].address_components[i].long_name;
                 }
                 else if (results[0].address_components[i].types[0] == "administrative_area_level_1") {
                   state = results[0].address_components[i].long_name;
                 }
                 */
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
   <input id = "button_reg" type="submit" value="SIGN UP"  />
   <script type="text/javascript">
     function check_input(){
        //first check basic format, like completeness of input
	 	if(sign_up.email_input.value == ""){
	    	alert("please fill any required blank");
			sign_up.email_input.focus();
			return false;
		}
	 	else if(sign_up.name_input.value == ""){
	    	alert("please fill any required blank");
	    	sign_up.name_input.focus();
			return false;
		}
	 	else if(sign_up.password.value == ""){
	    	alert("please fill any required blank");
	    	sign_up.password.focus();
			return false;
		}
	 	else if(sign_up.confirm_password.value == ""){
	    	alert("please fill any required blank");
	    	sign_up.confirm_password.focus();
			return false;
		}
	 	else if(sign_up.confirm_password.value != sign_up.password.value){
	    	alert("password did not match");
	    	sign_up.confirm_password.focus();
			return false;
		}
		//check whether user has choose a place
		else if(address == ""){
			alert("please spot your address");
			return false;
		}	
		//further check database about the email address
	 	var xmlhttp = new XMLHttpRequest();
        //sychronize mode
        xmlhttp.open("POST", "login_validate.php", false);
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xmlhttp.send("function=register_validate&email=" + sign_up.email_input.value);
        if(xmlhttp.responseText != "NULL"){
      	    alert("email has been registered!");
      	    sign_up.email_input.focus();
            return false;
        }
        //else{
            //when checking done, send data to php program and add records into the database
        	xmlhttp.open("POST", "login_validate.php", false);
        	xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xmlhttp.send("function=add_user&email=" + sign_up.email_input.value + 
                          "&latitude=" + pin_location.lat() + "&longitude=" + pin_location.lng() + 
                          "&name=" + sign_up.name_input.value + "&password=" + sign_up.password.value + 
                          "&street_addr=" + street_number + "&route=" + route + "&address=" + address);
            if(xmlhttp.responseText == "NOHOOD"){
            	alert("No corresponding hood in your selected area!");
                return false;
            }
            else if(xmlhttp.responseText != "SUCCESS"){
            	alert("Add user failed");
                return false;
            } 
        //}
  	    return true;
	 }
   </script>
   
   </form> 
  </div>
 </body>
</html>






