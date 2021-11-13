<?php
include('./classes/DB.php');
include('./classes/Login.php');

if(!Login::isLoggedIn()){
	die("Not logged in");
}

if(isset($_POST['confirm'])){
	if(isset($_POST['alldevices'])){

		DB::query('DELETE FROM socialnetwork.login_tokens WHERE user_id=:userid', array(':userid'=>Login::isLoggedIn()));
	}
	else{

		if(isset($_COOKIE['SNID'])){

			DB::query('DELETE FROM socialnetwork.login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['SNID'])));
		}
		if(isset($_COOKIE['SNID'])) {
		    unset($_COOKIE['SNID']);
		    unset($_COOKIE['SNID_']);
		    setcookie('SNID', null, -1, '/');
		    setcookie('SNID_', null, -1, '/');
		}

	}
	header("Location: login.php");
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
			<h1>Logout of your account</h1>
			<p class="logout_text">Are you sure you would like to logout?</p>
			<form class="logout_form" action="logout.php" method="post">
			<input type="checkbox" name="alldevices" value="">Logout of all devices?<br />
			<input class="default_button"type="submit" name="confirm" value="Confirm">
			</form>
		</div>
	</body>
</html>