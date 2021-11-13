<?php

class Notify{

	public static function createNotify($text = "", $postid = 0){

		$text = explode(" ", $text);
		$notify = array();

		foreach($text as $word){
			if(substr($word,0,1) == "@"){
				$notify[substr($word,1)]= array("type"=>1, "extra"=>' { "postbody": "'.htmlentities(implode(" ", $text)).'" } ');
			}

		}

		//neoptimizovan if uslov pri cemu se poziva funkcija
		//explode nepotrebno ali to je jedino resenje za sad
		//drugo me mrzi da smislim, al treba se popravi
		if($text == explode(" ","") && $postid != 0){
			$temp = DB::query('SELECT posts.user_id AS reciever, posts_likes.user_id AS sender FROM socialnetwork.posts, socialnetwork.posts_likes WHERE posts.id = posts_likes.post_id AND posts.id = :postid', array(':postid'=>$postid));

			$r = $temp[0]['reciever'];
			$s = $temp[0]['sender'];
			DB::query('INSERT INTO socialnetwork.notifications VALUES (null, :type, :reciever, :sender, :extra)', array(':type'=>2, ':reciever'=>$r, ':sender'=>$s, ':extra'=>""));
		}

		return $notify;
	}

}

?>