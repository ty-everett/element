<?php
// Element Network Message Sending

// TODO: notifications

include("../header.php");
requireLogin();

// make sure they passed in the information
if( !isset($_POST['recipient']) || 
		!isset($_POST['content']) ){
	err('Incorrect parameters.');
}

// sanitize
$recipient = strip_tags($_POST['recipient']);
$content = strip_tags($_POST['content']);

// make sure recipient is a username which is valid
if(strlen($recipient) > 20) {
	err("That user can\'t possibly exist.");
}

// verify user exists
$conn = dbc();
$statement = $conn->prepare('select * from users where username = ?;');
$statement->bind_param("s", $recipient);
$statement->execute();
$result = $statement->get_result();
if(mysqli_num_rows($result) != 1){
	err("That user doesn\'t appear to exist anymore... I mean I guess you could message them, but it\'d be like yelling into the void of cyberspace.");
}
unset($conn);

// verify this dumbass isn't trying to messaging themself for some reason
if($recipient == $_SESSION['username']){
	err('Please stop trying to message yourself. You\'re gonna break something.');
}

// put it in the database
$conn = dbw();
$statement = $conn->prepare("INSERT INTO messages (recipient, content) VALUES (?, ?);");
$statement->bind_param("ss", $recipient, $content);
$statement->execute();
notify($recipient, "You have a new message.");
echo("success");

?>