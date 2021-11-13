<?php
include('./classes/DB.php');
include('./classes/Login.php');

$tokenIsValid=false;
if(Login::isLoggedIn()){
	echo "Logged in as ";
	$username = DB::query('SELECT username FROM socialnetwork.users WHERE id=:userid',array(':userid'=>Login::isLoggedIn()))[0]['username'];
	echo $username;

	if(isset($_POST['changepassword'])){

		$oldpassword = $_POST['oldpassword'];
		$newpassword = $_POST['newpassword'];
		$newpasswordrepeat = $_POST['newpasswordrepeat'];

		$userid = Login::isLoggedIn();

		if(password_verify($oldpassword, DB::query('SELECT password FROM socialnetwork.users WHERE id=:userid', array(':userid'=>$userid))[0]['password'])){

			if($newpassword == $newpasswordrepeat){

				if(strlen($newpassword)>=6 && strlen($newpassword)<=60){

					DB::query('UPDATE socialnetwork.users SET password=:newpassword WHERE id=:userid',array(':newpassword'=>password_hash($newpassword, PASSWORD_BCRYPT),':userid'=>$userid));
					echo "<br>Password changed successfully!";

				}
				else{
					echo "Invalid password";
				}

			}
			else{
				echo "<br>Password are not same!";
			}

		}else{

			echo "<br>Inccorect old password";
		}

	}
}
else{
	if(isset($_GET['token'])){
		$token = $_GET['token'];

		if(DB::query('SELECT user_id FROM socialnetwork.password_tokens WHERE token=:token', array(':token'=>sha1($token)))){
			$userid = DB::query('SELECT user_id FROM socialnetwork.password_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
			$tokenIsValid=True;
			if(isset($_POST['changepassword'])){

			$newpassword = $_POST['newpassword'];
			$newpasswordrepeat = $_POST['newpasswordrepeat'];

				if($newpassword == $newpasswordrepeat){

					if(strlen($newpassword)>=6 && strlen($newpassword)<=60){

						DB::query('UPDATE socialnetwork.users SET password=:newpassword WHERE id=:userid',array(':newpassword'=>password_hash($newpassword, PASSWORD_BCRYPT),':userid'=>$userid));
						echo "<br>Password changed successfully!";
						DB::query('DELETE FROM socialnetwork.password_tokens WHERE user_id=:userid', array(':userid'=>$userid));

					}
					else{
						echo "Invalid password";
					}

				}
				else{
					echo "<br>Password are not same!";
				}
			}

		}
		else{
			die("Token invalid");
		}	
	}
	else{
			die("Not logged in");
		}
}

?>

<h1>Change your password</h1>
<form action="<?php if(!$tokenIsValid){ echo 'change-password.php';} else{echo 'change-password.php?token='.$token.'';} ?>" method="post">
	<?php if(!$tokenIsValid){ echo '<input type="password" name="oldpassword" value="" placeholder="Current passowrd"> <p/>';} ?>
	<input type="password" name="newpassword" value="" placeholder="New password"><p/>
	<input type="password" name="newpasswordrepeat" value="" placeholder="Repeat password"><p/>
	<input type="submit" name="changepassword" value="Confirm">
</form>