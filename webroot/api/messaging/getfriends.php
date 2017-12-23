<?php
// Element Network Friend Getter

// returns a list of friends

include('../header.php');
requireLogin();

// we require nothing new since we are already logged in

$conn = dbc();
$statement = $conn->prepare('SELECT content FROM friends WHERE recipient = ? AND status = 1;');
$statement->bind_param("s", $_SESSION['username']);
$statement->execute();
$result = $statement->get_result();
if(mysqli_num_rows($result) < 1){
	echo('none');
}else{
	while($row = mysqli_fetch_assoc($result)){
		echo($row['content'].'[Delimiator0]');
	}
}

?>