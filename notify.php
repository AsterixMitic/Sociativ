<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include('./classes/Image.php');
include('./classes/Notify.php');
include('./classes/Comment.php');

$username ="";
$verified = False; 
$isFollowing = False;

if(Login::isLoggedIn()){
	$username = DB::query('SELECT username FROM socialnetwork.users WHERE id=:userid',array(':userid'=>Login::isLoggedIn()))[0]['username'];

	$userid = Login::isLoggedIn();
}
else{
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
		<link rel = "icon" href = "site/logo_gradient.png"  type = "image/x-icon">
		<link rel="stylesheet" type="text/css" href="default-style.css">
		<link rel="stylesheet" type="text/css" href="post.css">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

	  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

	</head>

	<body class="basic">

		<header>
			<nav class="navbar">
				<img class="logo" src="site/logo_gradient.png">
			<form class="search" action="index.php" method="post">
					<input type="text" name="searchbox">
					<button type="submit" name="search" value=""><img src="site/search.png"></button>
				</form>
						<!--<a>Logged in as <?php echo $username; ?></a>-->
						<ul class="nav_links">
							<li><a href="index.php"><img src="site/home.png"></a></li>
							<li><a href="profile.php?username=<?php echo $username; ?>"><img src="site/profile.png"></a></li>
							<li><a href="my-messages.php"><img src="site/message.png"></a></li>
							<li id='active'><a href="notify.php"><img src="site/bell.png"></a></li>
							<li><a href="logout.php"><img src="site/logout.png"></a></li>
						</ul>
			</nav>
		</header>

		<?php

		echo "<div class='notification_title'><h1>Notifications</h1></div>";
if(DB::query('SELECT * FROM socialnetwork.notifications  WHERE reciever=:userid', array(':userid'=>$userid))){
	$notifications = DB::query('SELECT * FROM socialnetwork.notifications  WHERE reciever=:userid ORDER BY id DESC LIMIT 10', array(':userid'=>$userid));

	foreach($notifications as $n){
		echo "<div class='notification_post'>";
		if($n['type']==1){
			$s = DB::query('SELECT username FROM socialnetwork.users WHERE id=:sender', array(':sender'=>$n['sender']))[0]['username'];
			//print_r($s);
			if($n['extra'] == ""){
				echo "You got a notification!";
			}
			else{
				$extra = json_decode($n['extra']);
				echo Post::goToProfile($s)." has tagged you in 	his post.</br> ".$extra->postbody."";
			}

		}
		else if($n['type']==2){
			$s = DB::query('SELECT username FROM socialnetwork.users WHERE id=:sender', array(':sender'=>$n['sender']))[0]['username'];
			if($userid!=$s){
				echo Post::goToProfile($s)." has liked your post";
			}
		}
		echo "</div>";
	}
}


		?>

	</body>
</html>
