<?php
// Element Network Friend Removal

/*
username: the username of the person to remove
authcontent: the the "content" row of our user's field in the friendauth table
authkey: the revoke key
content: the content from the friends table

*/

include('../header.php');
requireLogin();

// we need the key that matches the hash and the username
if(!isset($_POST['username']) ||
		!isset($_POST['authcontent']) ||
		!isset($_POST['authkey']) || 
		!isset($_POST['content']) ){
	err("Parameters incorrect.");
}

// sanitize
$username = strip_tags($_POST['username']);
$authhash = hash("sha512", strip_tags($_POST['authkey']));
$content = strip_tags($_POST['content']);


// verify the information
$conn = dbc();
$statement = $conn->prepare("select * from friends where recipient = ? and auth = ?;");
$statement->bind_param("ss", $username, $authhash);
$statement->execute();
$result = $statement->get_result();
if(mysqli_num_rows($result) != 1){
	err("Invalid authkey or unknown username.");
}

// write to database
$conn = dbw();
$statement = $conn->prepare("DELETE FROM friends WHERE recipient = ? AND auth = ?;");
$statement->bind_param("ss", $username, $authhash);
$statement->execute();


// verify it was deleted
$conn = dbc();
$statement = $conn->prepare("select * from friends where recipient = ? and auth = ?;");
$statement->bind_param("ss", $username, $authhash);
$statement->execute();
$result = $statement->get_result();
if(mysqli_num_rows($result) != 1){
	// now we must delete the reference from the friendauth table
	$conn = dbw();
	$statement = $conn->prepare('DELETE FROM friendauth WHERE sender = ? AND content = ?;');
	$statement->bind_param("ss", $_SESSION['username'], strip_tags($_POST['authcontent']));
	$statement->execute();
	// now we set their status to 2 from our perspective
	// (so we don't see their stuff)
	$statement = $conn->prepare("UPDATE friends SET status = 2 WHERE recipient = ? AND content = ?;");
	$statement->bind_param("ss", $_SESSION['username'], $content);
	$statement->execute();
	echo("success");
}else{
	err("Not revoked.");
}

?>