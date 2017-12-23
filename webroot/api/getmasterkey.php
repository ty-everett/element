<?php
// Element Network Encrypted Master Key Provisioning

include('header.php');
requireLogin();
authenticateOrigin();

$conn = dbc();
$statement = $conn->prepare('SELECT masterkey FROM users WHERE username = ? LIMIT 1;');
$statement->bind_param('s', $_SESSION['username']);
$statement->execute();
$result = $statement->get_result();
$row = mysqli_fetch_assoc($result);
echo($row['masterkey']);

?>