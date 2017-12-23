<?php
// Element Network Messaging retrieval

include("../header.php");

requireLogin();

// make sure they passed a contactname
if(!isset($_POST['contactname'])){
	err('Incorrect parameters.');
}

// sanitize
$contactname = strip_tags($_POST['contactname']);

// connect to database
$conn = dbc();

$statement = $conn->prepare("SELECT * FROM messages WHERE recipient = ?;");
$statement->bind_param("s", $_SESSION['username']);
$statement->execute();
$result = $statement->get_result();
// iterate over our result
while($row = mysqli_fetch_assoc($result)){
	// echo out the message
	echo($row['content'].' ');
}
if(mysqli_num_rows($result) == 0){
	echo('none');
}

?>