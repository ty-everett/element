<?php
// Element Network Popularity Contest

include('../header.php');
requireLogin();

// we need the username for which we're getting popularity
if(!isset($_POST['username']){
	err('Incorrect parameters.');
}

// check popularity
$conn = dbc();
$statement = $conn->prepare('select count(*) from friends where recipient = ? and status = 1;');
$statement->prepare('s', strip_tags($_POST['username']));
$statement->execute();
$result = $statement->get_result();
$row = mysqli_fetch_assoc($result);
echo($row['count(*)']);
?>