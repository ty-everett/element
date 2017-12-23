<?php
// Element Network public key lookups

// we first include header functions
include("header.php");

// now we make sure we were passed a username
if(!isset($_POST['username'])){
	err("A username is required.");
}

// connect to database and look up the user
$conn = dbc();

$statement = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
$statement->bind_param("s", $_POST['username']);
$statement->execute();
$result = $statement->get_result();
if(mysqli_num_rows($result) != 1){
	err("User not found.");
}
$row = mysqli_fetch_assoc($result);
echo($row['publickey']);
?>