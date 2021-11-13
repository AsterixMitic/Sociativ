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

if(isset($_GET['username'])){
	if(DB::query('SELECT username FROM socialnetwork.users WHERE username=:username', array(':username'=>$_GET['username']))){

		$user_info = DB::query('SELECT * FROM socialnetwork.users WHERE username=:username', array(':username'=>$_GET['username']))[0];
		$userid = $user_info['id'];
		$username = $user_info['username'];
		$verified = $user_info['verified'];
		$profileImage = $user_info['profileimg'];

		$followerid = Login::isLoggedIn();
		$follower_info = DB::query('SELECT * FROM socialnetwork.users WHERE users.id=:userid',array(':userid'=>$followerid))[0];
		$followerusername = $follower_info['username'];


		$followers = DB::query('SELECT * FROM socialnetwork.followers, socialnetwork.users WHERE user_id=:userid AND follower_id=users.id', array(':userid'=>$userid));
		$followersSum = count($followers);

		$following = DB::query('SELECT * FROM socialnetwork.followers, socialnetwork.users WHERE follower_id=:userid AND user_id=users.id', array(':userid'=>$userid));
		$followingSum = count($following);


		if(isset($_POST['follow'])){

			if(!DB::query('SELECT follower_id FROM socialnetwork.followers WHERE user_id=:userid AND follower_id=:follower_id', array(':userid'=>$userid, ':follower_id'=>$followerid))){
					DB::query('INSERT INTO socialnetwork.followers VALUES (null,:userid,:followerid)', array(':userid'=>$userid, ':followerid'=>$followerid));
			}
			else{
				echo "Already following this user!";
			}
			$isFollowing = True;
			header("Refresh:0");
		}

		if(isset($_POST['unfollow'])){

				if(DB::query('SELECT follower_id FROM socialnetwork.followers WHERE user_id=:userid AND follower_id=:follower_id', array(':userid'=>$userid, ':follower_id'=>$followerid))){
							DB::query('DELETE FROM socialnetwork.followers WHERE user_id=:userid AND follower_id=:follower_id', array(':userid'=>$userid, ':follower_id'=>$followerid));
					}
					else{
						echo "User is unfollowed!";
					}
					$isFollowing = False;
					header("Refresh:0");
				}

		if(isset($_POST['send_message'])){

			$str = "Location: my-messages.php?sender=";
			$str.=$userid;
			header($str);

		}

		if(DB::query('SELECT follower_id FROM socialnetwork.followers WHERE user_id=:userid AND follower_id=:follower_id', array(':userid'=>$userid, ':follower_id'=>$followerid))){
				//echo "Already following this user!";
				$isFollowing = True;
			}

			if(isset($_POST['deletepost']) ){ 
					if(DB::query('SELECT id FROM socialnetwork.posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid))){

							DB::query('DELETE FROM socialnetwork.posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid));

							DB::query('DELETE FROM socialnetwork.posts_likes WHERE post_id=:postid', array(':postid'=>$_GET['postid']));
					}
			}

		if(isset($_POST['post'])){

			if($_FILES['postimg']['size']==0){
				Post::createPost($_POST['postbody'], Login::isLoggedIn(), $userid);
			}
			else{
				//echo "image";
				$postid = Post::createImgPost($_POST['postbody'], Login::isLoggedIn(), $userid);
				Image::uploadImage('postimg',$postid);
			}
		}


		if((isset($_POST['like']) || isset($_POST['unlike'])) && isset($_GET['postid'])){

				Post::likePost($_GET['postid'],$followerid);
		}

		if(isset($_POST['comment'])){

			Comment::createComment($_POST['commentbody'],$_GET['postid'],$followerid);
		}

		if(isset($_POST['edit'])){
			header("Location: my-account.php");
		}

		if(isset($_POST['searchbox'])){

		if(!empty($_POST['searchbox'])){
			$loc = "Location: search.php?search=";
			$loc.=$_POST['searchbox'];
			header($loc);
		}
	}

		$posts = Post::displayPosts($userid, $username, $followerid);

	}
	else{
		die('User not found!');
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
		<link rel="stylesheet" type="text/css" href="profile_style.css">
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
							<li><a href="index.php"><img src="site/home.png"></a></li>
							<li id='active'><a href="profile.php?username=<?php echo $followerusername; ?>"><img src="site/profile.png"></a></li>
							<li><a href="my-messages.php"><img src="site/message.png"></a></li>
							<li><a href="notify.php"><img src="site/bell.png"></a></li>
							<li><a href="logout.php"><img src="site/logout.png"></a></li>
						</ul>
			</nav>
		</header>


		<div class='upper_profile'>
			<div class='profile_text'>
			<?php if($profileImage!=null)
					echo "<div><img src='profile_images/".$profileImage."' style=' object-fit: cover;left;border-radius: 50%;width:100px; height:100px;'></div>"
			?>

				<div style="padding-left: 30px;">
					<h1> <?php echo $username; ?>'s Profile <?php if($verified){echo " - Verified!";}?></h1>
					<div>
						<div class="follow" id="followers">Followers: <?php echo $followersSum; ?></div>
						<div class="follow" id="following">Following: <?php echo $followingSum; ?></div>
					</div>
				</div>
			</div>
	</div>

	<div id="followersMod" class="popupfollow">
	  <div class="popupfollow-content">
	    <span class="close">&times;</span>
	    <h3>Followers</h3>
	    <?php
	    foreach($followers as $follow){
	    	echo "<div class='people'><img src='profile_images/".$follow['profileimg']."' class='people_image'><a href='profile.php?username=".$follow['username']."'>".$follow['username']."</a></div>";
	    }
	    ?>
	  </div>
	</div>

	<div id="followingMod" class="popupfollow">
	  <div class="popupfollow-content">
	    <span class="close">&times;</span>
	    <h3>Following</h3>
	    <?php
	    foreach($following as $follow){
	    	echo "<div class='people'><img src='profile_images/".$follow['profileimg']."' class='people_image'><a href='profile.php?username=".$follow['username']."'>".$follow['username']."</a></div>";
	    }
	    ?>
	  </div>
	</div>

	<script >

			var followersMod = document.getElementById("followersMod");
			var followingMod = document.getElementById("followingMod");

		var span = document.getElementsByClassName("close")[0];

		span.onclick = function() {
		  followingMod.style.display = "none";
		    followersMod.style.display = "none";
		}

		window.onclick = function(event) {
		  if (event.target == followingMod || event.target == followersMod) {
		    followingMod.style.display = "none";
		    followersMod.style.display = "none";
		  }
		}
			
			var followers = document.getElementById('followers');
			followers.style.cursor = 'pointer';
			followers.onclick = function() {
			    followersMod.style.display = "block";
			};

			var following = document.getElementById('following');
			following.style.cursor = 'pointer';
			following.onclick = function() {
			    followingMod.style.display = "block";
			};

		</script>

<form class="follow_form" action="profile.php?username=<?php echo $username; ?>" method="post">
	<?php
	if($userid != $followerid){	
		
		if(!$isFollowing){
			echo '<input class="default_button" type="submit" name="follow" value="Follow">';
		}
		else{
			echo '<input class="default_button" type="submit" name="unfollow" value="Unfollow">';
		}
		echo '<input class="default_button" type="submit" name="send_message" value="Send message">';
	}

	?>
</form>
	
	<?php
	if($userid == $followerid){
	?>

	<form class="edit" action="profile.php?username=<?php echo $username; ?>" method="post">
		<input class="default_button edit_button" type="submit" name="edit" value="Edit profile">
	</form>
	<div class="post_form">
		<form action="profile.php?username=<?php echo $username; ?>" method="post" enctype="multipart/form-data">

				<h3>What is on your mind?</h3>
				<textarea name="postbody" rows="8" cols="80"></textarea>
				Upload an image:
				<input type="file" name="postimg">
				<input id="post_button" type="submit" name="post" value="Post">
				
		</form>
	</div>;
	<?php
	}

	foreach($posts as $post){
		Post::displayPostProfile($post, $userid, $username, $follower_info);
	}
	 ?>

	</body>
</html>

