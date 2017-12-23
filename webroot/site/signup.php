<?php
require_once('/srv/http/element.tk/security/check.php');
// Temporary Element Network Email Collection

function err($message){
	echo('<!DOCTYPE html>
<html>
<head>
<title>Oops!</title>
<style>
body {
	font-family:helvetica;
}
</style>
<head>
<body>
<h1>Oops!</h1>
<p>'.$message.'</p>
</body>
</html>');
exit();
};

if(!isset($_POST['email'])){
	err("Make sure you entered an Email address"); // email was not defined
}
$email = $_POST['email'];
if(strlen($email) > 50){
	err('Your email was too long'); // email is >= 50 characters
}
if(strpos($email, "@") <= 0){
	err('Your email doesn\'t look right...'); // email has no @ sign
}
// put it in the database
$conn = new mysqli("localhost", "elementtemp", "asdpjfasdfpiasjpdfasdpfjioa", "elementtemp");
$email = mysqli_real_escape_string($conn, $email);
$email = strip_tags($email);
$statement = $conn->prepare("SELECT * FROM users WHERE email = ?;");
$statement->bind_param("s", $email);
$statement->execute();
$result = $statement->get_result();
if(mysqli_num_rows($result) != 0){
	err('We already got your email address! You\'re good to go!'); // email taken
}
$statement = $conn->prepare("INSERT INTO users (email) VALUES (?);");
$statement->bind_param("s", $email);
$statement->execute();

include("header.php");
printHeader("Thank You!");
?>
<div class="content">
<h1>Thank You!</h1>
<p>Thank you for your interest in this project! I'll be sure and keep you
updated whenever something happens. If you'd like to help out in any way,
don't hesitate to <a href="https://tyweb.us/contact/">get in touch</a>!</p>
</div>
<?php
include("footer.php");
printFooter();
?>