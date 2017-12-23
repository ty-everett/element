<?php
// Element Network Friend Rejection

include('../header.php');
requireLogin();

// we need the content of the request to we know which one to remove.
if(!isset($_POST['content'])){
	err('Incorrect parameters.');
}

// sanitize
$content = strip_tags($_POST['content']);

// remove the request from the database
$conn = dbw();
$statement = $conn->prepare('DELETE FROM friends WHERE recipient = ? AND content = ? AND status = 0;');
$statement->bind_param('ss', $_SESSION['username'], $content);
$statement->execute();
echo('success');

?>