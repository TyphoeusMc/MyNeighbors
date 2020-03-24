<?php
require "database.php";
if (isset($_POST['function']))
{
    if ($_POST['function'] == 'login_validate')
        login_validate();
    else if ($_POST['function'] == 'register_validate')
        register_validate();
    else if ($_POST['function'] == 'add_user')
        add_user();
    else if($_POST['function'] == 'quitSession')
        quitSession();
    else if($_POST['function'] == 'changeInfo')
        changeInfo();
    else
        echo "No such function";
}
function login_validate()
{
    global $mysql_connect;
    $email = $_POST['email'];
    $in_password = $_POST['password'];
    $get_password = $mysql_connect->prepare("select upassword, uname, uid, building, street, hood, lat, lng, address from users where email = ?");
    $get_password->bind_param('s', $email);
    $get_password->execute();
    $password_get = $get_password->get_result();
    if(mysqli_num_rows($password_get) <= 0)
        echo "NULL";
    else{
        $row = mysqli_fetch_assoc($password_get);
        $password = $row["upassword"];
        $uname = $row["uname"];
        $uid = $row["uid"];
        $hood = $row["hood"];
        $lat = $row["lat"];
        $lng = $row["lng"];
        $building = $row["building"];
        $street = $row["street"];
        $address = $row["address"];
        if($password == $in_password)
        {
            session_start();
            $_SESSION["admin"] = true;
            $_SESSION["uname"] = $uname;
            $_SESSION["email"] = $email;
            $_SESSION["password"] = $password;
            $_SESSION["uid"] = $uid;
            $_SESSION["hood"] = $hood;
            $_SESSION["lat"] = $lat;
            $_SESSION["lng"] = $lng;
            $_SESSION["building"] = $building;
            $_SESSION["street"] = $street;
            $_SESSION["address"] = $address;
            $_SESSION["bname"] = "";
            $_SESSION["b_id"] = "";
            $_SESSION["ib"] = "k";
            echo "SUCCESS";
        }
        else
            echo "FAIL";
    }
    mysqli_free_result($password_get);
    mysqli_close($mysql_connect);
}

function register_validate()
{
    global $mysql_connect;
    $email = $_POST['email'];
    $get_password = $mysql_connect->prepare("select * from users where email = ?");
    $get_password->bind_param('s', $email);
    $get_password->execute();
    $password_get = $get_password->get_result();
    if(mysqli_num_rows($password_get) <= 0)
        echo "NULL";
    else 
        echo "USED";
    mysqli_free_result($password_get);
    mysqli_close($mysql_connect);
}

function add_user()
{
    global $mysql_connect;
    //first chech if we can find corresponding hood
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $get_hood = $mysql_connect->prepare("select hid from hoods where sw_coord_la <= ?
                                             and sw_coord_lo <= ? and ne_coord_la > ?
                                             and ne_coord_lo > ?");
    $get_hood->bind_param('dddd', $latitude, $latitude, $longitude, $longitude);
    $get_hood->execute();
    $hood_get = $get_hood->get_result();
    if(mysqli_num_rows($hood_get) <= 0){
        echo "NOHOOD";
    }
    else{
        $row = mysqli_fetch_assoc($hood_get);
        $hood = $row["hid"];
        $email = $_POST['email'];
        $name = $_POST['name'];
        $password = $_POST['password'];
        $street_addr = $_POST['street_addr'];
        $route = $_POST['route'];
        $address = $_POST['address'];
        $adduser = $mysql_connect->prepare("insert into users values (999,?,?,999,?,?,null,?,null,?,false,?,?,?)");
        $adduser->bind_param("ssisisdds", $name, $password, $street_addr, $route, $hood, $email, $latitude, $longitude, $address);
        if($adduser->execute()){
            echo "SUCCESS";
        }
        else
            echo $adduser->error;
    }
    mysqli_free_result($hood_get);
    mysqli_close($mysql_connect);
}

function changeInfo()
{
    global $mysql_connect;
    //first chech if we can find corresponding hood
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $get_hood = $mysql_connect->prepare("select hid from hoods where sw_coord_la <= ?
                                             and sw_coord_lo <= ? and ne_coord_la > ?
                                             and ne_coord_lo > ?");
    $get_hood->bind_param('dddd', $latitude, $latitude, $longitude, $longitude);
    $get_hood->execute();
    $hood_get = $get_hood->get_result();
    if(mysqli_num_rows($hood_get) <= 0){
        echo "NOHOOD";
    }
    else{
        $row = mysqli_fetch_assoc($hood_get);
        $hood = $row["hid"];
        $uid = $_POST["uid"];
        //check whether user has change the hood
        $current_hood = $_POST["c_hood"];
        if($hood!=$current_hood)
        {
            //user quit his block
            $quit_block = $mysql_connect->prepare("delete from application where applicant = ?");
            $quit_block->bind_param("i", $uid);
            $quit_block->execute();
        }
        //if the user haven't create a profile, create one
        $profile = $_POST["profile"];
        $check_profile = $mysql_connect->prepare("select * from uprofile where profile_id = ?");
        $check_profile->bind_param("i", $uid);
        $check_profile->execute();
        $p_get = $check_profile->get_result();
        if(mysqli_num_rows($p_get) <= 0){
            $update_profile = $mysql_connect->prepare("insert into uprofile values (?, ?)");
            $update_profile->bind_param("is", $uid, $profile);
            if(!$update_profile->execute())
                echo "PFAIL";
        }
        else
        {
            $update_profile = $mysql_connect->prepare("update uprofile set content = ? where profile_id = ?");
            $update_profile->bind_param("si", $profile, $uid);
            if(!$update_profile->execute())
                echo "UPFAIL";
        }
        $name = $_POST['name'];
        $password = $_POST['password'];
        $street_addr = $_POST['street_addr'];
        $route = $_POST['route'];
        $address = $_POST['address'];
        $updateuser = $mysql_connect->prepare("update users set uname = ?, upassword = ?, building = ?, street = ?,
                                               hood = ?, lat = ?, lng = ?, address = ?, uprofile = ? where uid = ?");
        $updateuser->bind_param("ssisiddsii", $name, $password, $street_addr, $route, $hood, $latitude, $longitude, $address, $uid, $uid);
        if($updateuser->execute()){
            session_start();
            $_SESSION["uname"] = $name;
            $_SESSION["password"] = $password;
            $_SESSION["hood"] = $hood;
            $_SESSION["lat"] = $latitude;
            $_SESSION["lng"] = $longitude;
            $_SESSION["building"] = $street_addr;
            $_SESSION["street"] = $route;
            $_SESSION["address"] = $address;
            echo "SUCCESS";
        }
        else
            echo $updateuser->error;
    }
    mysqli_free_result($hood_get);
    mysqli_close($mysql_connect);
}

function quitSession()
{
    session_start();
    session_destroy();
    echo "done";
}











?>