<?php
    include('classes/DB.php');
    include('classes/Login.php');

    $username = '';
    $isFollowing = false;
    $verified = false;


    if (isset($_GET['username'])) {

        // $username = $_GET['username'];
        if (DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))) {

            $username = DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['username'];
            $userid = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];
            $verified = DB::query('SELECT verified FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['verified'];

            $followerid = Login::isLoggedIn();

            if (isset($_POST['follow'])) {

                if ($userid != $followerid) {
                    
                    // The bug was at the line below

                    if (!DB::query('SELECT id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {

                        if ($followerid == 5) {
                            DB::query('UPDATE users SET verified=1 WHERE id=:userid', array(':userid'=>$userid));
                    }

                        DB::query('INSERT INTO followers VALUES(\'\', :userid, :followerid)', array(':userid'=>$userid, ':followerid'=>$followerid));
                  
                    } 

                    $isFollowing = true;
                    // else {
                    //     echo "Already following!.";
                    // } 
                }
                
            }

            if (isset($_POST['unfollow'])) {

                if ($userid != $followerid) {
                    
                    // The bug was at the line below
                    if (DB::query('SELECT id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {

                        if ($followerid == 5) {
                            DB::query('UPDATE users SET verified=0 WHERE id=:userid', array(':userid'=>$userid));
                    }

                        DB::query('DELETE FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid));
                    }

                    $isFollowing = false;

                }      

            }
            
            
            // The bug was at the line below
            if (DB::query('SELECT id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
                
                // echo "Already following!.";
                $isFollowing = true;
            }

        } else {
            die('user not found!.');
        }
    }
?>
<h1><?php echo $username ?>'s Profile <?php if ($verified) {echo ' -verified';} ?></h1>
<form action="profile.php?username=<?php echo $username; ?>" method="post">
    <?php 
        if ($userid != $followerid) {
            if($isFollowing) {
                echo '<input type="submit" name="unfollow" value="Unfollow">';
            } else {
                echo '<input type="submit" name="follow" value="Follow">';
            }
        }
    ?>
</form>
