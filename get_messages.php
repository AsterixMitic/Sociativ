<?php

include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');

if(isset($_GET['sender'])){

	if(Login::isLoggedIn()){
		$username = DB::query('SELECT username FROM socialnetwork.users WHERE id=:userid',array(':userid'=>Login::isLoggedIn()))[0]['username'];

		$userid = Login::isLoggedIn();
	}
	else{
		header("Location: login.php");
	}

	$s = $_GET['sender'];
		$messagess = DB::query('SELECT messages.id, messages.body, s.username AS Sender, r.username AS Reciever FROM socialnetwork.messages LEFT JOIN socialnetwork.users s ON messages.sender = s.id LEFT JOIN socialnetwork.users r ON messages.reciever = r.id WHERE (r.id=:r AND s.id=:s) OR (r.id=:s AND s.id=:r)',array(':r'=>$userid, ':s'=>$s));

		$s_info = DB::query('SELECT * FROM socialnetwork.users WHERE users.id=:s',array(':s'=>$s))[0];

	$output="<div class='message_info'>
				<img src='profile_images/".$s_info['profileimg']."' class='people_image'>
				<a href='profile.php?username=".$s_info['username']."'>".$s_info['username']."</a>
			</div>";

	foreach($messagess as $message){
							if($message['Sender']==$username){
								$output .= '<div class="message_box your_message">
								<div class="message_recieved">'.Post::link_add($message['body']).'</div>';
							}
							else{
								$output .='<div class="message_box other_message">
								<div class="message_sent">'.Post::link_add($message['body']).'</div>';
							}
							$output .='</div>';
					}

		echo $output;
}
?>