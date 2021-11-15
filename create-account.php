 <?php

 include('classes/DB.php');

$pdo = new PDO('database', 'username', 'password');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if(isset($_POST['createaccount'])){
	$username = $_POST['username'];
	$password = $_POST['password'];
	$email = $_POST['email'];
	$image = "default.png";

	if(!DB::query('SELECT username FROM socialnetwork.users WHERE username=:username', array(':username'=>$username)))
	{

		if(strlen($username)>=3 && strlen($username)<=32){

			if(preg_match('/[a-zA-Z0-9_]+/',$username)){

				if(strlen($password)>=6 && strlen($password)<=60){

					if(filter_var($email,FILTER_VALIDATE_EMAIL)){

						if(!DB::query('SELECT email FROM socialnetwork.users WHERE email=:email', array(':email'=>$email))){

							DB::query('INSERT INTO socialnetwork.users VALUES (null,:username, :password, :email, 0, :image)', array(':username'=>$username, ':password'=>password_hash($password, PASSWORD_BCRYPT), ':email'=>$email, ':image'=>$image));

							echo'Succes';
						}
						else{
						echo "This email already exists!";
						}
					}
					else{
						echo "Invalid email";
						}
				
				}
				else{
					echo "Invalid password";
				}
			}
			else{
				echo "Invalid usernam";
			}
		}
		else{
			echo'Invalid username';
		}
	}
	else{
		echo 'User already exists!';
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
		<link rel = "icon" href = "site/logo.png"  type = "image/x-icon">
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	</head>
	<body class="basic">
		<div class="main">
			<h1> REGISTER </h1>
			<form action="create-account.php" method="post">
				<div class="txt_field">
					<input type="text" name="username" value="" placeholder="Username..."> <p/>
				</div>
				<div class="txt_field">
					<input type="password" name="password" value="" placeholder="Password..."><p/>
				</div>
				<div class="txt_field">
					<input type="email" name="email" value="" placeholder="Email..."><p/>
				</div>
			<input class="default_button" type="submit" name="createaccount" value="Create account">		
			</form>
		</div>
	</body>
</html>
