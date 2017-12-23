<?php
require_once('/srv/http/element.tk/security/check.php');

// Element Network Header Functions

// if isLoggedIn is not set, set it to zero.
if(!isset($_SESSION['isLoggedIn'])){
	$_SESSION['isLoggedIn'] = 0;
}

function dbc(){
	return new mysqli("localhost", "elementr", trim(file_get_contents("/cred/elementread.cred")), "element");
}

function dbw(){
	return new mysqli("localhost", "elementw", trim(file_get_contents("/cred/elementwrite.cred")), "element");
}

function err($error){
	echo('failure ' . $error);
	exit();
}

function requireLogin(){
	// we first need to make sure the user is logged in
	if($_SESSION['isLoggedIn'] != 1){
		header('location: /app/');
		err('Please log in first.');
	}
}
// alias ambiguity with old code
function require_login(){
	requireLogin();
}
function authenticate_origin(){
	authenticateOrigin();
}

function authenticateOrigin(){
	if($_GET['auth'] != $_SESSION['auth_random']){
		err('Invalid or missing auth token.');
	}
}

function redirect($page){
	header('location: '.$page);
	die();
}

/*

NOTIFICATION FUNCTION NEEDS UPDATING IF DEPLOYMENT IS TO OCCUR

*/

// notifications function
function notify($username, $message){
	// this function will notify the user of the message
	$conn = dbc();
	$statement = $conn->prepare('SELECT * FROM devices WHERE username = ?;');
	$statement->bind_param("s", $username);
	$statement->execute();
	$result = $statement->get_result();
	error_reporting(E_ERROR | E_PARSE);
	if(mysqli_num_rows($result) > 0){
		while($row = mysqli_fetch_assoc($result)){
			if($row['type'] == 'Android'){
				// send it with Firebase
				#API access key from Google API's Console
				define( 'API_ACCESS_KEY', 'GET_YOUR_OWN_API_KEY_FROM_GOOGLE_TO_SEND_NOTIFICATIONS' );
				$registrationIds = $row['id'];
				#prep the bundle
				$msg = array
					(
						'body' 	=> $message,
						//'title'	=> 'Element',
						'icon'	=> 'myicon',/*Default Icon*/
						'sound' => 'mySound'/*Default sound*/
					);
				$fields = array
					(
						'to'						=> $registrationIds,
						'notification'	=> $msg
					);
				
				$headers = array
					(
						'Authorization: key=' . API_ACCESS_KEY,
						'Content-Type: application/json'
					);
				#Send Reponse To FireBase Server	
				$ch = curl_init();
				curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
				curl_setopt( $ch,CURLOPT_POST, true );
				curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
				curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
				curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
				$result = curl_exec( $ch );
				curl_close( $ch );
				#Echo Result Of FireBase Server
				//echo $result;
			}
		}
	}
}

?>
