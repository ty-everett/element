<?php
// Element Search Function
include('header.php');

// require a query
if(!isset($_POST['query'])){
	err('Submit a query to preform a search.');
}

if(strlen($_POST['query']) > 20){
	err('Search is too long!');
}

// create a q variable
$q = '%'.$_POST['query'].'%';

// connect to DB, preform search
$conn = dbc();
$query = $conn->prepare('SELECT * FROM users WHERE username LIKE ? LIMIT 100;');
$query->bind_param('s', $q);
$query->execute();
$result = $query->get_result();
if(mysqli_num_rows($result) <= 0){
	echo('No results');
	die();
}else{
	while($row = mysqli_fetch_assoc($result)){
		if($row['username'] != $_SESSION['username']){
			echo($row['username'].',');
		}
	}
}
?>