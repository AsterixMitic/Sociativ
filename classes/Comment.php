<?php

class Comment{

	public static function createComment($commentbody, $postId, $userid){ 

			if(strlen($commentbody)>256 || strlen($commentbody)<1){
				die('Incorrect length');
			}

			if(!DB::query('SELECT id FROM socialnetwork.posts WHERE id=:postid', array(':postid'=>$postId))){
				echo "Invalid post ID";
			}
			else{
				DB::query('INSERT INTO socialnetwork.comment VALUES (null,:comment,:userid,NOW(),:postid)',array(':comment'=>$commentbody, ':userid'=>$userid,':postid'=>$postId));
			}
	}

	public static function displayComments($postId){


		$comments = DB::query('SELECT comment.comment, users.username, users.profileimg FROM socialnetwork.comment, socialnetwork.users WHERE comment.post_id=:postid AND comment.user_id=users.id', array(':postid'=>$postId));

		return $comments;
	}

}

?>