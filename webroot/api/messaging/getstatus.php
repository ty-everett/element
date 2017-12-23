<?php
include('../header.php');

// Element Network Status Returner for Friend/foe Determination

// returns the status of a ringsig in relation to the user.
// useful when checking if someone is a friend, blocked, or we've never heard of them before.

requireLogin();
authenticateOrigin();

if(!isset($_POST['ringsig'])){
	err('Ringsig required.');
}

$conn = dbc();
$statement = $conn->prepare('SELECT * FROM friends WHERE recipient = ? AND content = ? LIMIT 1;');
$statement->bind_param('ss', $_SESSION['username'], $_POST['ringsig']);
$statement->execute();
$result = $statement->get_result();
if(mysqli_num_rows($result) <= 0){
	echo('-1');
}else{
	$row = mysqli_fetch_assoc($result);
	echo($row['status']);
}
$conn->close();
// echo(','.$_POST['ringsig']); // debug
?>