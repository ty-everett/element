<?php

/* to be clear:

	|======================================================|
	|  THIS IS TO BE INCLUDED FIRST IN EVERY SINGLE FILE.  |
	|======================================================|
	
	THIS MEANS EVERY FILE EVERYWHERE NO MATTER WHAT SITE-WIDE.
	
	NO EXCEPTIONS.
	
*/


// start a PHP session if none exists
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

// check and set the $_SESSION['bot'] variable
if(!isset($_SESSION['bot'])){
	$_SESSION['bot'] = 1;
}

// define the isLoggedIn variable for security reasons
if(!isset($_SESSION['isLoggedIn'])){
	$_SESSION['isLoggedIn'] = 0;
}

// administer the browser check when needed.
if($_SESSION['bot'] == 1){
	$a = rand(0, 100000);
	$b = rand(0, 100000);
	$_SESSION['temp'] = hash('sha256', $a + $b);?>
<!DOCTYPE html>
<html lang="en-US">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checking Browser</title>
<style>
body {
	font-family:helvetica;
	padding-top:2em;
	text-align:center;
	color:yellow;
	background-color:#440044;
}
</style>
<script src="/security/hash.js"></script>
</head>
<body>
<h1>Loading...</h1>
<script>
var authHash = sha256((<?=$a?> + <?=$b?>).toString());
location.href = "/security/antispam.php?auth=" + authHash + "&orig=" + location.pathname;
</script>
</body>
</html>
<?php die();} ?>