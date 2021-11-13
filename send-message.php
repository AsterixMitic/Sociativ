<?php
session_start();
$cstrong = True;
$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));

if(!isset($_SESSION['token'])){
	$_SESSION['token'] = $token;
}

include('./classes/DB.php');
include('./classes/Login.php');

if(Login::isLoggedIn()){
	$username = DB::query('SELECT username FROM socialnetwork.users WHERE id=:userid',array(':userid'=>Login::isLoggedIn()))[0]['username'];

	$userid = Login::isLoggedIn();
}
else{
	header("Location: login.php");
}

if(isset($_POST['send'])){

	if(!isset($_POST['nocsrf'])){
		die("Invalid token");
	}

	if($_POST['nocsrf']!=$_SESSION['token']){
		die("Invalid token");
	}

	if(DB::query('SELECT id FROM socialnetwork.users WHERE id=:reciever', array(':reciever'=>htmlspecialchars($_GET['reciever'])))){
		DB::query('INSERT INTO socialnetwork.messages VALUES (null, :body, :sender, :reciever, 0)', array(':body'=>$_POST['body'], ':sender'=>$userid, ':reciever'=>htmlspecialchars($_GET['reciever'])));
		echo "Message sent";
	}
	else{
		die("User does not exists!");
	}
	session_destroy();
}

?>

<h1>Send a messages</h1>
<form action="send-message.php?reciever=<?php echo htmlspecialchars($_GET['reciever']); ?>" method="post">

	<textarea name="body" rows="8" cols="80"></textarea>
	<input type="hidden" name="nocsrf" value="<?php echo $_SESSION['token'];?>">
	<input type="submit" name="send" value="Send message">
	
</form>