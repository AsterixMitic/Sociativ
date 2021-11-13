<?php
include('classes/DB.php');

if(isset($_POST['login'])){
	$username = $_POST['username'];
	$password = $_POST['password'];

	if(DB::query('SELECT username FROM socialnetwork.users WHERE username=:username', array(':username'=>$username))){

			if(password_verify($password, DB::query('SELECT password FROM socialnetwork.users WHERE username=:username', array(':username'=>$username))[0]['password'])) {

				echo "Logged in!";
				$cstrong = True;
				$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
				//echo $token;

				$user_id = DB::query('SELECT id FROM socialnetwork.users WHERE username=:username', array(':username'=>$username))[0]['id'];

				DB::query('INSERT INTO socialnetwork.login_tokens VALUES (null,:token,:user_id)',array(':token'=>sha1($token), ':user_id'=>$user_id));

				setcookie("SNID", $token, time()+60*60*24*7, '/', NULL, NULL, TRUE);
				//drugo NULL se kasnije stavlja na TRUE ako se hostuje
				setcookie("SNID_",'1',time()+60*60*24*3, '/', NULL, NULL, TRUE);
				header("Location: index.php");

			}
			else{
				echo "Wrong password";
			}
	}
	else{
		echo "User not registerd";
	}
}

?>

<!DOCTYPE html>	

<html>
	<head>
		<meta charset="utf-8">
		<meta name="description" content="Social network">
  		<meta name="keywords" content="Social, Network, SocialNetwork">
  		<meta name="author" content="Aleksandar Mitic">

		<title>Sociativ</title>
		<link rel = "icon" href = "site/logo.png"  type = "image/x-icon">
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	</head>
	<body class="basic">
		<div class="main">
				<h1>Log in</h1>
			<form action="login.php" method="post">
			<div class="txt_field">
				<input type="text" name="username" value="" placeholder="Username..." required=""> 
			</div>
			<div class="txt_field">
			<input type="password" name="password" value="" placeholder="Password..." required="">
			</div>
			<div class="forgot">
				<a href='forgot-password.php'>Forgot password?</a>
			</div>
			<input class="default_button" type="submit" name="login" value="Log in" />
			<div class="signup_link">
				Not a memeber? 
				<a href='create-account.php'>Create account!</a>
			</div>
		</form>
		</div>
	</body>
</html>
