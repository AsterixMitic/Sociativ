<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include('./classes/Comment.php');
include('./classes/Notify.php');

$showTimeline = False;
if(Login::isLoggedIn()){
	$userid = Login::isLoggedIn();
	$user_info = DB::query('SELECT * FROM socialnetwork.users WHERE users.id=:userid',array(':userid'=>$userid))[0];
	$username = $user_info['username'];
	$showTimeline = True;
}
else{
	header("Location: login.php");
}


if((isset($_POST['like']) || isset($_POST['unlike'])) && isset($_GET['postid'])){


	Post::likePost($_GET['postid'],$userid);
}

if(isset($_POST['comment'])){

	Comment::createComment($_POST['commentbody'],$_GET['postid'],$userid);
}

if(isset($_POST['searchbox'])){

		if(!empty($_POST['searchbox'])){
			$loc = "Location: search.php?search=";
			$loc.=$_POST['searchbox'];
			header($loc);
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
		<link rel = "icon" href = "site/logo_gradient.png"  type = "image/x-icon">


		<link rel="stylesheet" type="text/css" href="default-style.css">
		<link rel="stylesheet" type="text/css" href="index_style.css">
		<link rel="stylesheet" type="text/css" href="post.css">
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
							<li id='active'><a href="index.php"><img src="site/home.png"></a></li>
							<li><a href="profile.php?username=<?php echo $username; ?>"><img src="site/profile.png"></a></li>
							<li><a href="my-messages.php"><img src="site/message.png"></a></li>
							<li><a href="notify.php"><img src="site/bell.png"></a></li>
							<li><a href="logout.php"><img src="site/logout.png"></a></li>
						</ul>
			</nav>
		</header>

<?php
$followingposts = DB::query('SELECT posts.id, posts.body,posts.likes, users.username, users.profileimg,posts.postimg, posts.posted_at FROM socialnetwork.users, socialnetwork.posts, socialnetwork.followers
WHERE posts.user_id = followers.user_id
AND posts.user_id = users.id
AND follower_id = :followerid
ORDER BY posts.posted_at DESC,posts.likes DESC;', array(':followerid'=>$userid));

foreach($followingposts as $post){

	//echo $post['body']." - ".$post['username']."</br></br>";php
	Post::displayPost($post, $userid,$user_info);
}

?>


</body>
</html>