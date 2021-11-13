<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');

if(Login::isLoggedIn()){
	$username = DB::query('SELECT username FROM socialnetwork.users WHERE id=:userid',array(':userid'=>Login::isLoggedIn()))[0]['username'];

	$userid = Login::isLoggedIn();
}
else{
	header("Location: login.php");
}

if(isset($_GET['sender'])){

	$s = $_GET['sender'];
	$messagess = DB::query('SELECT messages.id, messages.body, s.username AS Sender, r.username AS Reciever FROM socialnetwork.messages LEFT JOIN socialnetwork.users s ON messages.sender = s.id LEFT JOIN socialnetwork.users r ON messages.reciever = r.id WHERE (r.id=:r AND s.id=:s) OR (r.id=:s AND s.id=:r)',array(':r'=>$userid, ':s'=>$s));

	$s_info = DB::query('SELECT * FROM socialnetwork.users WHERE users.id=:s',array(':s'=>$s))[0];
}

if(isset($_POST['send'])){
	if(!empty($_POST['message-text'])){



		DB::query('INSERT INTO socialnetwork.messages VALUES(null, :body, :r, :s, 0)', array(':body'=>$_POST['message-text'], ':r'=>$userid, ':s'=>$s));
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
		<link rel="stylesheet" type="text/css" href="messages_style.css">
		<link rel="stylesheet" type="text/css" href="post.css">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

		<script>

			setInterval(()=>{ 
				let xhr = new XMLHttpRequest();
				xhr.open("GET", "get_messages.php?sender=<?php echo $s; ?>",true);
				xhr.onload = ()=>{
					if(xhr.readyState === XMLHttpRequest.DONE){
						if(xhr.status === 200){
							let data = xhr.response;
							document.querySelector("#messageBody").innerHTML = data;
							scroll();

						}
					}
				}

				let formData = new FormData(document.querySelector(".typing_area"));
				xhr.send(formData);
				}, 1000);
		</script>

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
							<li id='active'><a href="my-messages.php"><img src="site/message.png"></a></li>
							<li><a href="notify.php"><img src="site/bell.png"></a></li>
							<li><a href="logout.php"><img src="site/logout.png"></a></li>
						</ul>
			</nav>
		</header>

		<div class="left-side">
			
			<div class="chats">
				<?php

					$users_talked = DB::query('SELECT DISTINCT s.id as sid ,s.profileimg as spro,s.username as su, r.id as rid,r.profileimg as rpro,r.username as ru FROM socialnetwork.messages LEFT JOIN socialnetwork.users s ON s.id=messages.sender LEFT JOIN socialnetwork.users r ON r.id=messages.reciever WHERE (s.id = :userid OR r.id=:userid)',array(':userid'=>$userid));

					$u = array();
					foreach($users_talked as $user){
							if(!in_array(array('id'=>$user['sid'],'profileimg'=>$user['spro'], 'username'=>$user['su']), $u)){
								array_push($u, array('id'=>$user['sid'],'profileimg'=>$user['spro'], 'username'=>$user['su']));
							}
							else if(!in_array(array('id'=>$user['rid'],'profileimg'=>$user['rpro'], 'username'=>$user['ru']), $u)){
								array_push($u, array('id'=>$user['rid'],'profileimg'=>$user['rpro'], 'username'=>$user['ru']));
							}
					}


					foreach($u as $user){
						if($user['id']!=$userid){
						if(isset($s)){
							if($s==$user['id']){
									echo "<div class='people' id='active'><img src='profile_images/".$user['profileimg']."' class='people_image'><a href='my-messages.php?sender=".$user['id']."'>".$user['username']."</a></div>";
							}
							else{
								echo "<div class='people'><img src='profile_images/".$user['profileimg']."' class='people_image'><a href='my-messages.php?sender=".$user['id']."'>".$user['username']."</a></div>";
							}
						}
						else{
								echo "<div class='people'><img src='profile_images/".$user['profileimg']."' class='people_image'><a href='my-messages.php?sender=".$user['id']."'>".$user['username']."</a></div>";
						}
					}
				}
				?>
				
			</div>
		</div>


		<?php
			if(!empty($messagess) || isset($_GET['sender'])){
			echo "<div class='current_message' id='messageBody'>";
			echo "<div class='message_info'>
				<img src='profile_images/".$s_info['profileimg']."' class='people_image'>
				<a href='profile.php?username=".$s_info['username']."'>".$s_info['username']."</a>
			</div>";
				foreach($messagess as $message){
							if($message['Sender']==$username){
								echo '<div class="message_box your_message">
								<div class="message_recieved">'.Post::link_add($message['body']).'</div>';
							}
							else{
								echo'<div class="message_box other_message">
								<div class="message_sent">'.Post::link_add($message['body']).'</div>';
							}
							echo'</div>';
					}
				echo'</div>';
				echo "<div class='type-section'>
		<form class='typing_area'action='my-messages.php?sender=".$s."' method='post'>
				<input type='text' name='message-text' class='message-text'>
				<input class='send-button' type='submit' name='send' value='Send'>
			</form>
		</div>";

			}
			?>

	</body>
</html>

<script type="text/javascript">
	function scroll(){
		var messageBody = document.querySelector('#messageBody');
		messageBody.scrollTop = messageBody.scrollHeight - messageBody.clientHeight;
	}
	scroll();
</script>


<!--

	POKUSAJ PRAVLJENJA REALTIME PORUKA
	PUTEM PUSHER SERVISA ALI NEUSPESNO
	STOGA JE MOJ DRUGI POKUSAJ "REALTIME"
	ZAPRAVO INTERVAL SVAKE SEKUNDE

	JBG

	require __DIR__ . '/vendor/autoload.php';

	  $options = array(
	    'cluster' => 'eu',
	    'useTLS' => true
	  );
	  $pusher = new Pusher\Pusher(
	    '39fc434fd727bfc74cdb',
	    'eedf4bd6b2a5f0b6d234',
	    '1254171',
	    $options
	  );

	  $data = array(':body'=>$_POST['message-text'], ':r'=>$userid, ':s'=>$s);
	  $pusher->trigger('my-channel', 'my-event', $data);


<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
  <script>

    // Enable pusher logging - don't include this in production
    //Pusher.logToConsole = true;

    var pusher = new Pusher('39fc434fd727bfc74cdb', {
      cluster: 'eu'
    });

    var channel = pusher.subscribe('my-channel');
    channel.bind('my-event', function(data) {
    	if(userid == data.:s){
    	  //alert(JSON.stringify(data));
    	  alert('sender');
  		}
    });
  </script>
-->

<!--
	OVO JE JOS GORI NACIN DA SE NAPRAVI REALTIME
	
	<script type="text/javascript">
	setTimeout(function(){
   window.location.reload(1);
}, 5000);
</script>
 -->

