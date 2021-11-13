<?php

class Post{

	public static function createPost($postbody, $loggedUserId, $profileUserId){ 

			if(strlen($postbody)>256 || strlen($postbody)<1){
				die('Incorrect length');
			}

			$topics = self::getTopics($postbody);

			if($loggedUserId == $profileUserId){

				if(count(Notify::createNotify($postbody))!=0){
					foreach(Notify::createNotify($postbody) as $key => $n){
						$s = $loggedUserId;
						$r = DB::query('SELECT id FROM socialnetwork.users WHERE username=:username', array(':username'=>$key))[0]['id'];
						if($r!=0){
							DB::query('INSERT INTO socialnetwork.notifications VALUES (null, :type, :reciever, :sender, :extra)', array(':type'=>$n["type"], ':reciever'=>$r, ':sender'=>$s, ':extra'=>$n["extra"]));
						}
					}
				}

				DB::query('INSERT INTO socialnetwork.posts VALUES (null, :postbody, NOW(), :userid, 0, null, :topics)', array(':postbody'=>$postbody,':userid'=>$profileUserId, ':topics'=>$topics));
			}
			else{
				die("Incorrect profile!");
			}
	}

	public static function likePost($postid, $liker){

		if(!DB::query('SELECT user_id FROM socialnetwork.posts_likes WHERE post_id=:postid AND user_id=:userid',array(':postid'=>$postid, ':userid'=>$liker))){

				DB::query('UPDATE socialnetwork.posts SET likes=likes+1 WHERE id=:postid', array(':postid'=>$postid));

				DB::query('INSERT INTO socialnetwork.posts_likes VALUES (null, :postid, :userid)', array(':postid'=>$postid, ':userid'=>$liker));
				Notify::createNotify("", $postid);

			}
			else{

				DB::query('UPDATE socialnetwork.posts SET likes=likes-1 WHERE id=:postid', array(':postid'=>$postid));

				DB::query('DELETE FROM socialnetwork.posts_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postid, ':userid'=>$liker));
			}
	}

	public static function getTopics($text){

		$text = explode(" ", $text);
		$topics="";

		foreach($text as $word){
			if(substr($word,0,1) == "#"){
				$topics.= substr($word, 1).",";
			}
		}

		return $topics;
	}


	public static function link_add($text){

		$text = explode(" ", $text);
		$newstring = "";

		foreach($text as $word){
			if(substr($word,0,1) == "@"){
				$newstring.= "<a href='profile.php?username=".substr($word,1)."'>".htmlspecialchars($word)."</a>&nbsp;";
			}
			else if(substr($word,0,1) == "#"){
				$newstring.= "<a href='search.php?search=".substr($word,1)."'>".htmlspecialchars($word)."</a>&nbsp;";
			}
			else if(substr($word,0,4)=="http"){
				$newstring.= "<a href='".$word."'>".htmlspecialchars($word)."</a>&nbsp;";
			}
			else{
			$newstring.=htmlspecialchars($word)." ";
			}
		}
		return $newstring;
	}

	public static function goToProfile($text){
		$newstring = "";
		$newstring .= "<a href='profile.php?username=".$text."'>".$text."</a> ";
		return $newstring;
	}

	public static function displayPosts($userid, $username, $loggedUserId){
		$dbposts = DB::query('SELECT posts.id, posts.body,posts.likes, users.username, users.profileimg,posts.postimg, posts.posted_at FROM socialnetwork.users, socialnetwork.posts WHERE posts.user_id = users.id AND users.id = :userid ORDER BY posts.posted_at DESC,posts.likes DESC;', array(':userid'=>$userid));

		return $dbposts;
	}

	public static function displayPost($post, $userid, $user_info){
		echo "<div class='post'>";
	echo "<div class='top_post'><img class='user_img' src='profile_images/".$post['profileimg']."'><div class='top_post_text'><p class='profile_username'>".self::goToProfile($post['username'])."</p><p class='post_date'>".$post['posted_at']."</p></div></div><p class='post_text'>".self::link_add($post['body'])."</p>";
	if($post['postimg']!=null){

		echo "<img id='myImg' class='post_image' src='images/".$post['postimg']."'> ";
	}
	echo "<form action='index.php?postid=".$post['id']."' method='post'>
		<div class='likes'>";
		if(!DB::query('SELECT post_id FROM socialnetwork.posts_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$post['id'], ':userid'=>$userid)))
			{
				echo"<button type='submit' name='like' value=''><img src='heart_empty.png'/></button>";
			}
			else{
				echo"<button type='submit' name='unlike' value=''><img src='heart_full.png'/></button>";
			}
				 echo"<span> ".$post['likes']." likes</span> </div>
				 <div class='make_comment'>
				 	<form class='make_comment_form'action='index.php?postid=".$post['id']."' method='post'>
				 		<img class='comment_img' src='profile_images/".$user_info['profileimg']."'>
				 		<input type='text' name='commentbody' placeholder='Write comment'>
				 		<input class='comment_button'type='submit' name='comment' value='Comment'>
				 	</form>
				 	</div>
				 	";
				 	$comments = Comment::displayComments($post['id']);
				 	if($comments!=null){
				 		if(count($comments)>3){
				 			/*echo "<button class='show-more-comments'>Show more comments</button>";*/
				 		}
				 		echo "<div id='comment-section'>";
						 	foreach($comments as $comment){
								echo "<div class='comment'><img class='comment_img' src='profile_images/".$comment['profileimg']."'>".Post::link_add("@".$comment['username'])." - ".$comment['comment']."</div></br>";
							}
					}
				 	echo"</div></form></div>";

	}

	public static function displayPostProfile($post, $userid, $username, $follower_info){
		echo "<div class='post'>";
	echo "<div class='top_post'><img class='user_img' src='profile_images/".$post['profileimg']."'><div class='top_post_text'><p class='profile_username'>".self::goToProfile($post['username'])."</p><p class='post_date'>".$post['posted_at']."</p></div>";
	if($userid==$follower_info['id']){
		echo "<div class='close_button'> <form action='profile.php?username=".$username."&postid=".$post['id']."' method='post'><button onClick=\"javascript: return confirm('Post will be deleted');\" type='submit' name='deletepost' value=''>X</button></form></div>";

	}

	echo "</div><p class='post_text'>".self::link_add($post['body'])."</p>";
	if($post['postimg']!=null){

		echo "<img class='post_image' src='images/".$post['postimg']."'>";
	}
	echo "<form action='profile.php?username=".$username."&postid=".$post['id']."' method='post'>
		<div class='likes'>";
		if(!DB::query('SELECT post_id FROM socialnetwork.posts_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$post['id'], ':userid'=>$follower_info['id'])))
			{
				echo"<button type='submit' name='like' value=''><img src='heart_empty.png'/></button>";
			}
			else{
				echo"<button type='submit' name='unlike' value=''><img src='heart_full.png'/></button>";
			}
				 echo"<span> ".$post['likes']." likes</span> </div>";
			 	if(isset($follower_info)){
				 "<div class='make_comment'>
				 	<form class='make_comment_form'action='profile.php?username=".$username."&postid=".$post['id']."' method='post'>
				 		<img class='comment_img' src='profile_images/".$follower_info['profileimg']."'>
				 		<input type='text' name='commentbody' placeholder='Write comment'>
				 		<input class='comment_button'type='submit' name='comment' value='Comment'>
				 	</form>
				 	</div>                       
				 	";
				 	}
				 	$comments = Comment::displayComments($post['id']);
				 	if($comments!=null){
				 		if(count($comments)>3){
				 			/*echo "<button class='show-more-comments'>Show more comments</button>";*/
				 		}
				 		echo "<div id='comment-section'>";
						 	foreach($comments as $comment){
								echo "<div class='comment'><img class='comment_img' src='profile_images/".$comment['profileimg']."'>".Post::link_add("@".$comment['username'])." - ".$comment['comment']."</div></br>";
							}
					}
				 	echo"</div></form></div>";
	}

	public static function displaySearchProfile($userid){
		$user_info = DB::query('SELECT * FROM socialnetwork.users WHERE id=:userid',array(':userid'=>$userid));

		$followersSum = DB::query('SELECT COUNT(user_id) AS num FROM socialnetwork.followers WHERE user_id=:userid', array(':userid'=>$userid));

		$followingSum = DB::query('SELECT COUNT(follower_id) AS num FROM socialnetwork.followers WHERE follower_id=:userid', array(':userid'=>$userid));

		echo "<div class='user_post'>";
		echo "<div class='user_top_post'><img class='user_img_search' src='profile_images/".$user_info[0]['profileimg']."'><div class'user_top_post_text'><p class='profile_username'>".Post::goToProfile($user_info[0]['username'])."</p><p>Followers: ".$followingSum[0]['num']." Following:".$followingSum[0]['num']."</p></div></div>";
		
	 	echo"</div>";
	}

	public static function displaySearchPost($post, $user_info){
		echo "<div class='post'>";
	echo "<div class='top_post'><img class='user_img' src='profile_images/".$post['profileimg']."'><div class='top_post_text'><p class='profile_username'>".self::goToProfile($post['username'])."</p><p class='post_date'>".$post['posted_at']."</p></div></div><p class='post_text'>".self::link_add($post['body'])."</p>";
	if($post['postimg']!=null){

		echo "<img class='post_image' src='images/".$post['postimg']."'>";
	}
	echo "<form action='search.php?search=".$_GET['search']."&postid=".$post['id']."' method='post'>
		<div class='likes'>";
		if(!DB::query('SELECT post_id FROM socialnetwork.posts_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$post['id'], ':userid'=>$user_info['id'])))
			{
				echo"<button type='submit' name='like' value=''><img src='heart_empty.png'/></button>";
			}
			else{
				echo"<button type='submit' name='unlike' value=''><img src='heart_full.png'/></button>";
			}
				 echo"<span> ".$post['likes']." likes</span> </div>
				 <div class='make_comment'>
				 	<form class='make_comment_form'action='search.php?search=".$_GET['search']."&postid=".$post['id']."' method='post'>
				 		<img class='comment_img' src='profile_images/".$user_info['profileimg']."'>
				 		<input type='text' name='commentbody' placeholder='Write comment'>
				 		<input class='comment_button'type='submit' name='comment' value='Comment'>
				 	</form>
				 	</div>
				 	";
				 	$comments = Comment::displayComments($post['id']);
				 	if($comments!=null){
				 		if(count($comments)>=3){
				 			/*echo "<button class='show-more-comments'>Show more comments</button>";*/
				 		}
				 		echo "<div id='comment-section'>";
						 	foreach($comments as $comment){
								echo "<div class='comment'><img class='comment_img' src='profile_images/".$comment['profileimg']."'>".Post::link_add("@".$comment['username'])." - ".$comment['comment']."</div></br>";
							}
					}
				 	echo"</div></form></div>";
	}

	public static function createImgPost($postbody, $loggedUserId, $profileUserId){ 

			if(strlen($postbody)>256){
				die('Incorrect length');
			}

			$topics = self::getTopics($postbody);

			if($loggedUserId == $profileUserId){

				if(count(Notify::createNotify($postbody))!=0){
					foreach(Notify::createNotify($postbody) as $key => $n){
						$s = $loggedUserId;
						$r = DB::query('SELECT id FROM socialnetwork.users WHERE username=:username', array(':username'=>$key))[0]['id'];
						if($r!=0){
							DB::query('INSERT INTO socialnetwork.notifications VALUES (null, :type, :reciever, :sender, :extra)', array(':type'=>$n["type"], ':reciever'=>$r, ':sender'=>$s, ':extra'=>$n["extra"]));
						}
					}
				}

				DB::query('INSERT INTO socialnetwork.posts VALUES (null, :postbody, NOW(), :userid, 0,null,:topics)', array(':postbody'=>$postbody,':userid'=>$profileUserId, ':topics'=>$topics));
				$postid = DB::query('SELECT id FROM socialnetwork.posts WHERE user_id=:userid ORDER BY id DESC LIMIT 1', array(':userid'=>$loggedUserId))[0]['id'];
				return $postid;
			}
			else{
				die("Incorrect profile!");
			}
	}

}

?>