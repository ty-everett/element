<?php
session_start();
if(!isset($_GET['orig'])){
	$_GET['orig'] = "/";
}
if($_SESSION['temp'] == $_GET['auth']){
	$_SESSION['bot'] = 0;
	unset($_SESSION['temp']);
	header('location: ' . $_GET['orig']);
}else{
	echo('Please make sure your browser is using JavaScript.');
}
?>