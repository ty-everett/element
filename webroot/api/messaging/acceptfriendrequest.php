<?php
// Element Network Friend Request Accepting

/*
contetlookup: friends table "content" field from original request
username: username of original requester

content: the content of our new request back to the original requester
authhash: the sha512 hash of the revoke key of the returning request
authcontent: a copy of the revoke key encrypted to the masterkey

*/

include('../header.php');
requireLogin();

// we need a copy of the "content" field to match with
if(!isset($_POST['contentlookup']) ||
		!isset($_POST['username']) ||
		!isset($_POST['content']) ||
		!isset($_POST['authhash']) ||
		!isset($_POST['authcontent']) ||
		!isset($_POST['profilekeys']) ){
	err("Incorrect parameters.");
}

// sanitize
$contentlookup = strip_tags($_POST['contentlookup']);
$recipient = strip_tags($_POST['username']);
$content = strip_tags($_POST['content']);
$authhash = strip_tags($_POST['authhash']);
$authcontent = strip_tags($_POST['authcontent']);
$profilekeys = strip_tags($_POST['profilekeys']);

// verify the information
$conn = dbc();
$statement = $conn->prepare("select * from friends where recipient = ? and content = ?;");
$statement->bind_param("ss", $_SESSION['username'], $contentlookup);
$statement->execute();
$result = $statement->get_result();
if(mysqli_num_rows($result) != 1){
	err("Invalid content or unknown username.");
}

// insert our own request with status 1


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

$statement = $conn->prepare("INSERT INTO friends (recipient, content, status, auth, profilekeys) VALUES (?, ?, 1, ?, ?);");
$statement->bind_param("ssss", $recipient, $content, $authhash, $profilekeys);
$statement->execute();

// notify the user
notify($recipient, "Someone accepted your friend request.");

// create a new revoke key for our use
$statement = $conn->prepare("INSERT INTO friendauth (sender, content) VALUES (?, ?);");
$statement->bind_param("ss", $_SESSION['username'], $authcontent);
$statement->execute();

// update status on their request
$statement = $conn->prepare("UPDATE friends SET status = 1 WHERE recipient = ? AND content = ?;");
$statement->bind_param("ss", $_SESSION['username'], $contentlookup);
$statement->execute();

// success
echo("success");




?>