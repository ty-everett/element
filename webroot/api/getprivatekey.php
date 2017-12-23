<?php
// Element Network Encrypted Private Key Provisioning

include('header.php');
requireLogin();
authenticateOrigin();

$conn = dbc();
$statement = $conn->prepare('SELECT privatekey FROM users WHERE username = ? LIMIT 1;');
$statement->bind_param('s', $_SESSION['username']);
$statement->execute();
$result = $statement->get_result();
$row = mysqli_fetch_assoc($result);
echo($row['privatekey']);

?>