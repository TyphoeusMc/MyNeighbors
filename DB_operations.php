<?php
session_start();
require "database.php";
if (isset($_POST['function']))
{
    if ($_POST['function'] == 'checkUserBlock')
        checkUserBlock();
    else if ($_POST['function'] == 'getUserInfo')
        getUserInfo();
    else if ($_POST['function'] == 'applyBlock')
        applyBlock();
    else if ($_POST['function'] == 'cancelApp')
        cancelApp();
    else if ($_POST['function'] == 'approveApp')
        approveApp();
    else if ($_POST['function'] == 'getSubjects')
        getSubjects();
    else if ($_POST['function'] == 'postThread')
        postThread();
    else if ($_POST['function'] == 'checkFriend')
        checkFriend();
    else if ($_POST['function'] == 'cancelFA')
        cancelFA();
    else if ($_POST['function'] == 'approveFA')
        approveFA();
    else if ($_POST['function'] == 'applyF')
        applyF();
    else if ($_POST['function'] == 'checkThread')
        checkThread();
    else if ($_POST['function'] == 'getThreadInfo')
        getThreadInfo();
    else
        echo "No such function";
}

function getThreadInfo()
{
    global $mysql_connect;
    $thread = $_POST["thread"];
    $get_thread = $mysql_connect->prepare("select title, content, uname, email, ptime from thread, users, message
                                           where tid = ? and thread.author = users.uid and thread.ini_message = message.m_id");
    $get_thread->bind_param('i', $thread);
    $get_thread->execute();
    $thread_get = $get_thread->get_result();
    $dom = new DOMDocument("1.0");
    $node = $dom->createElement("threads");
    $parnode = $dom->appendChild($node);
    header("Content-type: text/xml");
    if(mysqli_num_rows($thread_get) > 0)
    {
        while($row = mysqli_fetch_assoc($thread_get))
        {
            $node = $dom->createElement("thread");
            $newnode = $parnode->appendChild($node);
            $newnode->setAttribute("content",$row['content']);
            $newnode->setAttribute("title",$row['title']);
            $newnode->setAttribute("author",$row['uname']);
            $newnode->setAttribute("ptime",$row['ptime']);
            $newnode->setAttribute("email",$row['email']);
        }
    }
    echo $dom->saveXML();
}

function getUserInfo()
{
    //session_start();
    global $mysql_connect;
    $get_user = $mysql_connect->prepare("select content from uprofile where profile_id = ? ");
    $get_user->bind_param('i', $_SESSION["uid"]);
    $get_user->execute();
    $user_get = $get_user->get_result();
    if(mysqli_num_rows($user_get) <= 0){
        echo "NULL";
    }
    else{
        $row = mysqli_fetch_assoc($user_get);
        echo $row["content"];
    }
}

function checkThread()
{
    //get every thread that the user can read
    $uid = $_SESSION["uid"];
    global $mysql_connect;
    $get_thread = $mysql_connect->prepare("select tid, content, title, uname, ptime, lastpost from thread, subjects, users 
                                           where users.uid = thread.author and thread.topic_id = subjects.sid and tid in (select thread from recipient where users = ?)");
    $get_thread->bind_param("i", $uid);
    if(!$get_thread->execute())
        echo $get_thread->error;
    $thread_get = $get_thread->get_result();
    $dom = new DOMDocument("1.0");
    $node = $dom->createElement("threads");
    $parnode = $dom->appendChild($node);
    header("Content-type: text/xml");
    if(mysqli_num_rows($thread_get) > 0)
    {
        while($row = mysqli_fetch_assoc($thread_get))
        {
            $node = $dom->createElement("thread");
            $newnode = $parnode->appendChild($node);
            $newnode->setAttribute("tid",$row['tid']);
            $newnode->setAttribute("content",$row['content']);
            $newnode->setAttribute("title",$row['title']);
            $newnode->setAttribute("author",$row['uname']);
            $newnode->setAttribute("ptime",$row['ptime']);
            $newnode->setAttribute("lastpost",$row['lastpost']);
        }
    }
    echo $dom->saveXML();
}

function cancelFA()
{
    global $mysql_connect;
    $uid = $_POST["uid"];
    $target = $_POST["target"];
    $cfa = $mysql_connect->prepare("delete from friendship where applicant = ? and target = ? ");
    $cfa->bind_param('ii', $uid, $target);
    if(!$cfa->execute())
        echo $cfa->error;
}

function approveFA()
{
    global $mysql_connect;
    $uid = $_POST["uid"];
    $target = $_POST["target"];
    $afa = $mysql_connect->prepare("update friendship set approved = 1 where applicant = ? and target = ? ");
    $afa->bind_param('ii', $target, $uid);
    if(!$afa->execute())
        echo $afa->error;
}
function applyF()
{
    global $mysql_connect;
    $uid = $_POST["uid"];
    $target = $_POST["target"];
    $af = $mysql_connect->prepare("insert into friendship values(?,?,false) ");
    $af->bind_param('ii', $uid, $target);
    if(!$af->execute())
        echo $af->error;
}

function checkFriend()
{
    $uid = $_SESSION["uid"];
    global $mysql_connect;
    $dom = new DOMDocument("1.0");
    $node = $dom->createElement("friends");
    $parnode = $dom->appendChild($node);
    header("Content-type: text/xml");
    //check 1.current approved friends 2.unapproved friend 3.friends to approve
    $get_friend = $mysql_connect->prepare("select target as recip, uname, email from friendship, users where friendship.applicant = ? and friendship.approved = 1 and friendship.target = users.uid
                                           union
                                           select applicant as recip, uname, email from friendship, users where friendship.target = ? and friendship.approved = 1 and friendship.applicant = users.uid");
    $get_friend->bind_param('ii', $uid, $uid);
    $get_friend->execute();
    $friend_get = $get_friend->get_result();
    if(mysqli_num_rows($friend_get) > 0)
    {
        while($row = mysqli_fetch_assoc($friend_get))
        {
            $node = $dom->createElement("friend");
            $newnode = $parnode->appendChild($node);
            $newnode->setAttribute("uid",$row['recip']);
            $newnode->setAttribute("uname",$row['uname']);
            $newnode->setAttribute("email",$row['email']);
        }
    }
    //waiting for approved
    $get_friend = $mysql_connect->prepare("select target as recip, uname, email from friendship, users where friendship.applicant = ? 
                                           and friendship.approved = 0 and friendship.target = users.uid");
    $get_friend->bind_param('i', $uid);
    $get_friend->execute();
    $friend_get = $get_friend->get_result();
    if(mysqli_num_rows($friend_get) > 0)
    {
        while($row = mysqli_fetch_assoc($friend_get))
        {
            $node = $dom->createElement("unapprove");
            $newnode = $parnode->appendChild($node);
            $newnode->setAttribute("uid",$row['recip']);
            $newnode->setAttribute("uname",$row['uname']);
            $newnode->setAttribute("email",$row['email']);
        }
    }
    //need user's approvement
    $get_friend = $mysql_connect->prepare("select applicant as recip, uname, email from friendship, users where friendship.target = ?
                                           and friendship.approved = 0 and friendship.applicant = users.uid");
    $get_friend->bind_param('i', $uid);
    $get_friend->execute();
    $friend_get = $get_friend->get_result();
    if(mysqli_num_rows($friend_get) > 0)
    {
        while($row = mysqli_fetch_assoc($friend_get))
        {
            $node = $dom->createElement("toapprove");
            $newnode = $parnode->appendChild($node);
            $newnode->setAttribute("uid",$row['recip']);
            $newnode->setAttribute("uname",$row['uname']);
            $newnode->setAttribute("email",$row['email']);
        }
    }
    //can apply
    $get_friend = $mysql_connect->prepare("select uid as recip, uname, email from application, users where application.approved = 1 and users.uid <> ?
                                           and application.applicant = users.uid and blocks = (select blocks from application where applicant = ?)
                                           and uid not in (select target from friendship where applicant = ?)");
    $get_friend->bind_param('iii', $uid, $uid, $uid);
    $get_friend->execute();
    $friend_get = $get_friend->get_result();
    if(mysqli_num_rows($friend_get) > 0)
    {
        while($row = mysqli_fetch_assoc($friend_get))
        {
            $node = $dom->createElement("canapprove");
            $newnode = $parnode->appendChild($node);
            $newnode->setAttribute("uid",$row['recip']);
            $newnode->setAttribute("uname",$row['uname']);
            $newnode->setAttribute("email",$row['email']);
        }
    }
    echo $dom->saveXML();
}

function postThread()
{
    global $mysql_connect;
    //get all data first
    $latitude = $_POST["latitude"];
    $longitude = $_POST["longitude"];
    $title = $_POST["title"];
    $subject = $_POST["subject"];
    $content = $_POST["content"];
    $target = $_POST["target"];
    $uid = $_POST["uid"];
    $address = $_POST["address"];
    $hood = $_POST["hood"];
    //first deal with subject
    if($subject=="")
        $subject = "DEFAULT";
    $get_subject = $mysql_connect->prepare("select sid from subjects where hood = ? and content = ?");
    $get_subject->bind_param('is', $hood, $subject);
    $get_subject->execute();
    $subject_get = $get_subject->get_result();
    if(mysqli_num_rows($subject_get) <= 0){
        $add_subject = $mysql_connect->prepare("insert into subjects values(999, ?, ?)");
        $add_subject->bind_param('si', $subject, $hood);
        if(!$add_subject->execute())
           echo $add_subject->error; 
    }
    $get_subject = $mysql_connect->prepare("select sid from subjects where hood = ? and content = ?");
    $get_subject->bind_param('is', $hood, $subject);
    $get_subject->execute();
    $subject_get = $get_subject->get_result();
    $row = mysqli_fetch_assoc($subject_get);
    $sid = $row["sid"];
    //then create a new thread
    $currentTime = date('Y-m-d h:i:s', time());
    $create_thread = $mysql_connect->prepare("insert into thread (tid, topic_id, title, ini_message, author, ptime, lat, lastpost, lng, address)
                                              values(999, ?, ?, null, ?, ?, ?, ?, ?, ?)");
    $create_thread->bind_param('isisdsds', $sid, $title, $uid, $currentTime, $latitude, $currentTime, $longitude, $address);
    if(!$create_thread->execute())
        echo $create_thread->error;
    //get tid of thread that just created
    $get_thread = $mysql_connect->prepare("select tid from thread where author = ? and ini_message is null");
    $get_thread->bind_param('i', $uid);
    $get_thread->execute();
    $tid_get = $get_thread->get_result();
    $row = mysqli_fetch_assoc($tid_get);
    $tid = $row["tid"];
    //use the tid to create the message
    $create_message = $mysql_connect->prepare("insert into message values(999, ?, ?, ?, null, ?)");
    $create_message->bind_param('issi', $uid, $currentTime, $content, $tid);
    if(!$create_message->execute())
        echo $create_thread->error;
    //get message id of the new message
    $get_message = $mysql_connect->prepare("select m_id from message where author = ? and thread = ?");
    $get_message->bind_param('ii', $uid, $tid);
    $get_message->execute();
    $mid_get = $get_message->get_result();
    $row = mysqli_fetch_assoc($mid_get);
    $mid = $row["m_id"];
    //update the thread
    $update_thread = $mysql_connect->prepare("update thread set ini_message = ? where tid = ?");
    $update_thread->bind_param('ii', $mid, $tid);
    if(!$update_thread->execute())
        echo $update_thread->error;
    //control target
    $get_target = "";
    if($target=="hood"){
        $get_target = $mysql_connect->prepare("select uid as recip from users where hood = ?");
        $get_target->bind_param('i', $hood);
        if(!$get_target->execute())
            echo $get_target->error;
    }
    else if($target=="block"){
        $get_target = $mysql_connect->prepare("select applicant as recip from application where approved = 1 and 
                                               blocks = (select blocks from application where applicant = ?)");
        $get_target->bind_param('i', $uid);
        if(!$get_target->execute())
            echo $get_target->error;
    }
    else if($target=="friend"){
        $get_target = $mysql_connect->prepare("select target as recip from friendship where applicant = ? and approved = 1
                                               union
                                               select applicant as recip from friendship where target = ? and approved = 1");
        $get_target->bind_param('ii', $uid, $uid);
        if(!$get_target->execute())
            echo $get_target->error;
    }
    else{
        $get_target = $mysql_connect->prepare("select user2 as recip from neighborhood where user1 = ?");
        $get_target->bind_param('i', $uid);
        if(!$get_target->execute())
            echo $get_target->error;
    }
    $target_get = $get_target->get_result();
    if(mysqli_num_rows($target_get) > 0)
    {
        $insert_recip = $mysql_connect->prepare("insert into recipient values(?, ?)");
        while($row = mysqli_fetch_assoc($target_get))
        {
            $insert_recip->bind_param('ii', $tid, $row['recip']);
            if(!$insert_recip->execute())
                echo $get_target->error;
        }
    }
    echo "SUCCESS";
}

function getSubjects()
{
    global $mysql_connect;
    $hood = $_POST["hood"];
    $get_subject = $mysql_connect->prepare("select content from subjects where hood = ? ");
    $get_subject->bind_param('i', $hood);
    $get_subject->execute();
    $subject_get = $get_subject->get_result();
    if(mysqli_num_rows($subject_get) <= 0){
        mysqli_free_result($subject_get);
        mysqli_close($mysql_connect);
        echo "NULL";
    }
    else{
        $dom = new DOMDocument("1.0");
        $node = $dom->createElement("subjects");
        $parnode = $dom->appendChild($node);
        header("Content-type: text/xml");
        while($row = mysqli_fetch_assoc($subject_get))
        {
            $node = $dom->createElement("subject");
            $newnode = $parnode->appendChild($node);
            $newnode->setAttribute("content",$row['content']);
        }
        mysqli_free_result($subject_get);
        mysqli_close($mysql_connect);
        echo $dom->saveXML();
    }
}

function cancelApp()
{
    $uid = $_POST["uid"];
    global $mysql_connect;
    $block_app = $mysql_connect->prepare("delete from application where applicant=?");
    $block_app->bind_param('i', $uid);
    if($block_app->execute())
    {
        //session_start();
        //$_SESSION["bname"] = "NULL";
        //$_SESSION["b_id"] = "NULL";
        echo "SUCCESS";
    }
    else
        echo $block_app->error;
}

function approveApp()
{
    $uid = $_POST["uid"];
    $applicant = $_POST["applicant"];
    global $mysql_connect;
    $block_app = $mysql_connect->prepare("insert into approval values(?,?)");
    $block_app->bind_param('ii', $uid, $applicant);
    if($block_app->execute())
    {
        //session_start();
        //$_SESSION["bname"] = "NULL";
        //$_SESSION["b_id"] = "NULL";
        echo "SUCCESS";
    }
    else
        echo $block_app->error;
}

function applyBlock()
{
    $b_id = $_POST["b_id"];
    $uid = $_POST["uid"];
    global $mysql_connect;
    $block_app = $mysql_connect->prepare("insert into application values (?,?,0)");
    $block_app->bind_param('ii', $uid, $b_id);
    if($block_app->execute())
        echo "SUCCESS";
    else
        echo $block_app->error;
}

function checkUserBlock()
{
    //session_start();
    $uid = $_SESSION["uid"];
    global $mysql_connect;
    $get_app = $mysql_connect->prepare("select applicant, blocks, approved, bname from application, blocks where applicant = ? and application.blocks = blocks.b_id");
    $get_app->bind_param('i', $uid);
    $get_app->execute();
    $app_get = $get_app->get_result();
    if(mysqli_num_rows($app_get) <= 0){
        $hood = $_SESSION["hood"];
        $get_applicable_blocks = $mysql_connect->prepare("select b_id, bname from blocks where hood = ?");
        $get_applicable_blocks->bind_param('i', $hood);
        $get_applicable_blocks->execute();
        $app_blocks_get = $get_applicable_blocks->get_result();
        if(mysqli_num_rows($app_blocks_get) > 0)
        {
            //return applicable blocks to the user using an XML file
            $dom = new DOMDocument("1.0");
            $node = $dom->createElement("blocks");
            $parnode = $dom->appendChild($node);
            header("Content-type: text/xml");
            $node = $dom->createElement("blockInfo");
            $newnode = $parnode->appendChild($node);
            $newnode->setAttribute("ib","n");
            $newnode->setAttribute("bname", "NULL");
            $newnode->setAttribute("b_id", "NULL");
            while($row = mysqli_fetch_assoc($app_blocks_get))
            {
                $node = $dom->createElement("block");
                $newnode = $parnode->appendChild($node);
                $newnode->setAttribute("b_id",$row['b_id']);
                $newnode->setAttribute("bname",$row['bname']);
            }
        }
        //user haven't apply for any block, return blocks he can apply for
        mysqli_free_result($app_get);
        mysqli_close($mysql_connect);
        $_SESSION["ib"] = "n";
        session_write_close();
        echo $dom->saveXML();
    }
    else{
        $row = mysqli_fetch_assoc($app_get);
        $bname = $row["bname"];
        $b_id = $row["blocks"];
        //if the user has join a block, list all other users in that block
        if($row["approved"] == 1)
        {
            $get_users_in_blocks = $mysql_connect->prepare("select uid, uname from users, application where blocks = ? and users.uid = application.applicant and application.approved = 0");
            $get_users_in_blocks->bind_param('i', $b_id);
            $get_users_in_blocks->execute();
            $users_blocks_get = $get_users_in_blocks->get_result();
            $dom = new DOMDocument("1.0");
            $node = $dom->createElement("users");
            $parnode = $dom->appendChild($node);
            header("Content-type: text/xml");
            
            $node = $dom->createElement("blockInfo");
            $newnode = $parnode->appendChild($node);
            $newnode->setAttribute("ib", "y");
            $newnode->setAttribute("bname", $bname);
            $newnode->setAttribute("b_id", $b_id);
            if(mysqli_num_rows($users_blocks_get) > 0)
            {
                while($row = mysqli_fetch_assoc($users_blocks_get))
                {
                    $node = $dom->createElement("user");
                    $newnode = $parnode->appendChild($node);
                    $newnode->setAttribute("uid",$row['uid']);
                    $newnode->setAttribute("uname",$row['uname']);
                }
            }
            $_SESSION["bname"] = $bname;
            $_SESSION["b_id"] = $b_id;
            $_SESSION["ib"] = "y";
            mysqli_free_result($app_get);
            mysqli_close($mysql_connect);
            session_write_close();
            echo $dom->saveXML();
        }
        //if the user has applied for a block but not yet been approved, show the state.
        else
        {
            $dom = new DOMDocument("1.0");
            $node = $dom->createElement("users");
            $parnode = $dom->appendChild($node);
            header("Content-type: text/xml");
            $node = $dom->createElement("blockInfo");
            $newnode = $parnode->appendChild($node);
            $newnode->setAttribute("ib", "NOT");
            $newnode->setAttribute("bname", $bname);
            $newnode->setAttribute("b_id", $b_id);
            $_SESSION["bname"] = $bname;
            $_SESSION["b_id"] = $b_id;
            mysqli_free_result($app_get);
            mysqli_close($mysql_connect);
            session_write_close();
            echo $dom->saveXML();
        }
    }
    
}





?>