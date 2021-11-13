<?php
include('./classes/DB.php');

if(isset($_POST['resetpassword'])){

	//moze i PHP-ova mail() funkcija
	$cstrong = True;
	$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
	$email = $_POST['email'];
	$user_id = DB::query('SELECT id FROM socialnetwork.users WHERE email=:email', array(':email'=>$email))[0]['id'];

	DB::query('INSERT INTO socialnetwork.password_tokens VALUES (null,:token,:user_id)',array(':token'=>sha1($token), ':user_id'=>$user_id));
	echo "Email sent!";
	echo "<br>";
	echo $token;
}

?>
<!DOCTYPE html>	

<html>
	<head>
		<meta charset="utf-8">
		<title>Social Network</title>
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	</head>
	<body class="basic">
		<div class="main">
			<h1>Forgot password</h1>
			<form action="forgot-password.php" method="post">
				<div class="txt_field">
					<input type="text" name="email" value="" placeholder="Email..."><p/>
				</div>
				<input class="default_button" type="submit" name="resetpassword" value="Reset password">
			</form>
		</div>
	</body>
</html>