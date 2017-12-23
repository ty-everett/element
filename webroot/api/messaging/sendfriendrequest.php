<?php
// Element Network Friend Request Sending

include("../header.php");
requireLogin();
authenticateOrigin();

// we first need to make sure they passed us a valid username
if(!isset($_POST['username']) ||
		!isset($_POST['content']) || // ringsig
		!isset($_POST['authhash']) || // sha512(authkey)
		!isset($_POST['authcontent']) || // authkey encrypted with masterkey
		!isset($_POST['profilekeys']) ){ // access to profile info
	err("Incorrect parameters.");
}

// sanitize
$recipient = strip_tags($_POST['username']);
$content = strip_tags($_POST['content']);
$authhash = strip_tags($_POST['authhash']);
$authcontent = strip_tags($_POST['authcontent']);
$profilekeys = strip_tags($_POST['profilekeys']);

// make sure username is valid
if(strlen($recipient) > 20) {
	err("Invalid username.");
}

// verify user exists
$conn = dbc();
$statement = $conn->prepare('select * from users where username = ?;');
$statement->bind_param("s", $recipient);
$statement->execute();
$result = $statement->get_result();
if(mysqli_num_rows($result) != 1){
	err("User does not exist.");
}
unset($conn);

// put it in the database
$conn = dbw();

$statement = $conn->prepare("INSERT INTO friends (recipient, content, status, auth, profilekeys) VALUES (?, ?, 0, ?, ?);");
$statement->bind_param("ssss", $recipient, $content, $authhash, $profilekeys);
$statement->execute();

$statement = $conn->prepare("INSERT INTO friendauth (sender, content) VALUES (?, ?);");
$statement->bind_param("ss", $_SESSION['username'], $authcontent);
$statement->execute();

// notify the recipient
notify($recipient, "You have a new friend request.");

echo("success");
?>