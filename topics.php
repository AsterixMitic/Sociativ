<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include('./classes/Image.php');

if(isset($_GET['topic'])){

	if(DB::query("SELECT topics FROM socialnetwork.posts WHERE FIND_IN_SET(:topics, topics)", array('topics'=>$_GET['topic']))){

		$posts = DB::query("SELECT * FROM socialnetwork.posts, socialnetwork.users WHERE posts.user_id = users.id AND FIND_IN_SET(:topics, topics)", array('topics'=>$_GET['topic']));

		foreach($posts as $post){

			if($post['postimg']!=null){
		echo Post::link_add($post['body'])." - ".Post::goToProfile($post['username'])."</br></br>"."<img src='images/".$post['postimg']."' style=' max-width:500px; max-height:500px;'></br></br>";
			}
			else{
				echo Post::link_add($post['body'])." - ".Post::goToProfile($post['username'])."</br></br>";
			}

		}

	}

}

?>