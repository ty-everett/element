<?php
// Element Network Messaging retrieval

include("../header.php");
requireLogin();

// make sure they passed a contactname
if(!isset($_POST['amount'])){
	err('Parameters incorrect.');
}

// sanitize
$amount = strip_tags($_POST['amount']);
if(is_nan($amount)){
	err('Amount is not a number.');
}

// connect to database
$conn = dbc();

$statement = $conn->prepare("SELECT * FROM messages WHERE recipient = ?;");
$statement->bind_param("s", $_SESSION['username']);
$statement->execute();
$result = $statement->get_result();
// set our count variable to only print the last 8.
$count = 0;
// iterate over our result
while($row = mysqli_fetch_assoc($result)){
	$count++; // subtract 1 to count each time
	if((mysqli_num_rows($result) - $count) > $amount){
		// if the number of rows minus the number of iterations is more than 20, keep going.
		continue;
	}
	// echo out the message
	echo($row['content'].' ');
}
if(mysqli_num_rows($result) == 0){
	echo('none');
}

?>