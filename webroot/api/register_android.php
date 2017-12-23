<?php
include('header.php');
requireLogin();
if(!isset($_POST['id'])){
	err('no id given');
}
$conn = dbc();
$statement = $conn->prepare('SELECT * FROM devices WHERE id = ?;');
$statement->bind_param('s', $_POST['id']);
$statement->execute();
$result = $statement->get_result();
if(mysqli_num_rows($result) < 1){
	$conn = dbw();
	$statement = $conn->prepare('INSERT INTO devices (registered, type, username, id) VALUES (now(), "Android", ?, ?);');
	$statement->bind_param('ss', $_SESSION['username'], $_POST['id']);
	$statement->execute();
	echo('success');
}else{
	echo('got it');
}
?>