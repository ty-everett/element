<?php
include('header.php');

// Element Network Ring Signature Random Public Key Provider

// returns some random public keys for use in ring signatures for friend requests
// server returns 11, client only uses 5, so server doesn't know which ones client uses.

//requireLogin();
//authenticateOrigin();

$conn = dbc();
$statement = $conn->prepare('select publickey from users order by rand() limit 11;');
$statement->execute();
$result = $statement->get_result();
while($row = mysqli_fetch_assoc($result)){
	echo($row['publickey'].'[Delimiator]');
}

?>