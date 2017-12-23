<?php
// Element Network Registration

// include the header functions
include("header.php");

// we need to make sure everything was passed correctly
if( !isset($_POST['publickey']) ||
		!isset($_POST['privatekey']) ||
		!isset($_POST['masterkey']) ||
		!isset($_POST['recoverykey']) ||
		!isset($_POST['username']) || 
		!isset($_POST['password']) ){
	err("Something isn't right... try again?");
}

// either the email or the phone number is required but not both
if( !isset($_POST['email']) && !isset($_POST['phone']) ){
	err("Make sure you entered an email or a phone number!");
}

// now we need to try to do input validation even though
// everything is encrypted for the most part
$conn = dbc();
$statement = $conn->prepare("SELECT * FROM users WHERE username = ? OR publickey = ? OR privatekey = ? OR masterkey = ? OR recoverykey = ?;");
$statement->bind_param("sssss", $_POST['username'], $_POST['publickey'], $_POST['privatekey'], $_POST['masterkey'], $_POST['recoverykey'] );
$statement->execute();
$result = $statement->get_result();
if($result->num_rows !== 0){
	err("That username is already taken!");
}
if(isset($_POST['email'])){
	$statement = $conn->prepare("SELECT * FROM users WHERE email = ?");
	$statement->bind_param("s", $_POST['email']);
	$statement->execute();
	$result = $statement->get_result();
	if($result->num_rows !== 0){
		err("Someone's already using that email address!");
	}
}else{
	$statement = $conn->prepare("SELECT * FROM users WHERE phonenumber = ?;");
	$statement->bind_param("s", $_POST['phone']);
	$statement->execute();
	$result = $statement->get_result();
	if($result->num_rows !== 0){
		err("Someone is already using that phone number!");
	}
}

// now we do some sanitization
$username = strip_tags(strtolower($_POST['username']));
$publickey = strip_tags($_POST['publickey']);
$privatekey = strip_tags($_POST['privatekey']);
$masterkey = strip_tags($_POST['masterkey']);
$recoverykey = strip_tags($_POST['recoverykey']);
$password = strip_tags($_POST['password']);

$salt = rand(10000000, 99999999);

// we need to sanitize the phone number or email address
if(isset($_POST['email'])){
	$email = strip_tags($_POST['email']);
	
	// now we're ready to put it all in the database
	$password = hash('sha256', $password . $salt);
	$conn = dbw();
	$statement = $conn->prepare("INSERT INTO users (username, publickey, privatekey, masterkey, recoverykey, email, password, passwordsalt) VALUES (?, ?, ?, ?, ?, ?, ?, ?);");
	$statement->bind_param("ssssssss", $username, $publickey, $privatekey, $masterkey, $recoverykey, $email, $password, $salt);
	$statement->execute();
	$conn->close();
	// set up session variables
	$_SESSION['isLoggedIn'] = 1;
	$_SESSION['username'] = $username;
}else{
	$phone = strip_tags($_POST['phone']);
	
	// now we're ready to put it all in the database
	$password = hash('sha256', $password . $salt);
	$conn = dbw();
	$statement = $conn->prepare("INSERT INTO users (username, publickey, privatekey, masterkey, recoverykey, phonenumber, password, passwordsalt) VALUES (?, ?, ?, ?, ?, ?, ?, ?);");
	$statement->bind_param("ssssssss", $username, $publickey, $privatekey, $masterkey, $recoverykey, $phone, $password, $salt);
	$statement->execute();
	$conn->close();
	// set up session variables
	$_SESSION['isLoggedIn'] = 1;
	$_SESSION['username'] = $username;
}
$_SESSION['auth_random'] = rand(1, 100000);
// We did it!
echo("success");

?>