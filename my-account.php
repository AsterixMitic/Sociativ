<?php

include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Image.php');

	if(Login::isLoggedIn()){
		$username = DB::query('SELECT username FROM socialnetwork.users WHERE id=:userid',array(':userid'=>Login::isLoggedIn()))[0]['username'];
		$userid = Login::isLoggedIn();
		$profileImage = DB::query('SELECT profileimg FROM socialnetwork.users WHERE id=:userid', array(':userid'=>$userid));
	}
	else{
		die("Not logged in");
	}

	if(isset($_POST['uploadprofileimg'])){

		Image::uploadProfileImage('profileimg',$userid);
		header("Refresh:0");

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
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
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
							<li id=><a href="index.php"><img src="site/home.png"></a></li>
							<li><a href="profile.php?username=<?php echo $username; ?>"><img src="site/profile.png"></a></li>
							<li><a href="my-messages.php"><img src="site/message.png"></a></li>
							<li><a href="notify.php"><img src="site/bell.png"></a></li>
							<li><a href="logout.php"><img src="site/logout.png"></a></li>
						</ul>
			</nav>
		</header>

		<h1> My account </h1>
<form action="my-account.php" method="post" enctype="multipart/form-data">
	Upload profile image:
	<input type="file" name="profileimg">
	<input type="submit" name="uploadprofileimg" value="Upload image"> </br></br>

	<?php if($profileImage!=null)
		echo "<img src='profile_images/".$profileImage[0]['profileimg']."' style=' max-width:500px; max-height:500px;'>"
	?>
	
</form>
	</body>
</html>