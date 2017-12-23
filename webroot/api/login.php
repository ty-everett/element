<?php
// Element Network Login Script

// include header functions
include("header.php");

// we need to make sure we are being passed correct information
if( !isset($_POST['username']) && 
		!isset($_POST['email']) && 
		!isset($_POST['phone']) ){
	err("You've entered an incorrect username, email, phone number, or password. Try again?"); // non-descript error due to security through obscurity
}

// we also need to make sure a password was sent
if(!isset($_POST['password'])){
	err("You've entered an incorrect username, email, phone number, or password. Try again?");
}

// now we determine what method of authentication is being used
if(isset($_POST['username'])){
	// loggging in with a username
	$username = strip_tags(strtolower($_POST['username']));
	$password = strip_tags($_POST['password']);
	// now we connnect to the database
	$conn = dbc();
	$statement = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1;");
	$statement->bind_param("s", $username);
	$statement->execute();
	$result = $statement->get_result();
	if(mysqli_num_rows($result) != 1){
		err("You've entered an incorrect username, email, phone number, or password. Try again?");
	}
	$row = mysqli_fetch_assoc($result);
	// now we validate the password
	if(hash('sha256', $password . $row['passwordsalt']) != $row['password']){
		err("You've entered an incorrect username, email, phone number, or password. Try again?");
	}
	// set up session variables
	$_SESSION['isLoggedIn'] = 1;
	$_SESSION['username'] = $row['username'];
	$_SESSION['auth_random'] = rand(1, 100000);
	
	// echo username out and the encrypted master key
	echo('success');
	
	// notify user
	notify($_SESSION['username'], "You just logged into your account.");
	
	$conn->close();
}else if(isset($_POST['email'])){
	// logging in with an email address
	$username = strip_tags($_POST['email']);
	$password = strip_tags($_POST['password']);
	// now we connnect to the database
	$conn = dbc();
	$statement = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1;");
	$statement->bind_param("s", $username);
	$statement->execute();
	$result = $statement->get_result();
	if(mysqli_num_rows($result) != 1){
		err("You've entered an incorrect username, email, phone number, or password. Try again?");
	}
	$row = mysqli_fetch_assoc($result);
	// now we validate the password
	if(hash('sha256', $password . $row['passwordsalt']) != $row['password']){
		err("You've entered an incorrect username, email, phone number, or password. Try again?");
	}
	// set up session variables
	$_SESSION['isLoggedIn'] = 1;
	$_SESSION['username'] = $row['username'];
	$_SESSION['auth_random'] = rand(1, 100000);
	// echo username out and the encrypted master key
	echo('success');
	
	// notify the user of the login
	notify($_SESSION['username'], "You just logged into your account.");
	
	$conn->close();
}else{
	// logging in with a phone number
	$username = strip_tags($_POST['phone']);
	$password = strip_tags($_POST['password']);
	// now we connnect to the database
	$conn = dbc();
	$statement = $conn->prepare("SELECT * FROM users WHERE phonenumber = ? LIMIT 1;");
	$statement->bind_param("s", $username);
	$statement->execute();
	$result = $statement->get_result();
	if(mysqli_num_rows($result) != 1){
		err("You've entered an incorrect username, email, phone number, or password. Try again?");
	}
	$row = mysqli_fetch_assoc($result);
	// now we validate the password
	if(hash('sha256', $password . $row['passwordsalt']) != $row['password']){
		err("You've entered an incorrect username, email, phone number, or password. Try again?");
	}
	// echo username out and the encrypted master key
	echo('success');
	// set up session variables
	$_SESSION['isLoggedIn'] = 1;
	$_SESSION['username'] = $row['username'];
	$_SESSION['auth_random'] = rand(1, 100000);
	$conn->close();
	
	// notify user
	notify($_SESSION['username'], "You just logged into your account.");
	
}
?>