<?php
require_once('/srv/http/element.tk/security/check.php');
include('header.php');
require_login();
?>
$(document).ready(function(){
	
	
	/* variable names used 
	
		mk masterkey
		pw password
		pub publickey
		priv privatekey
		
		_ck cryptokey (imported or generated)
		_db compressed (json_compress)
		_pl plaintext (exported)
		_edb encrypted database (compressed, encrypted)
		
	*/
	
	
	// some global variables
	var username = 'not set';
	var pub_dbe = 'not set';
	var pub_ck = 'not set';
	
	// hide hidden stuff and redo the CSS on things
	$('.content').css({
		marginTop: $('.header').height()
	});
	
	// register the device
	if(typeof Android == 'object'){
		$.ajax({
			url: '/api/register_android.php?auth=<?=$_SESSION['auth_random']?>',
			type: 'POST',
			data: {id: Android.getDeviceID()},
			success: function(result) {}
		});
	}
	
	// now we start in on all the crypto stuff
	// let's ask the server for our username first
	$.ajax({
		url: '/api/getusername.php?auth=<?=$_SESSION['auth_random']?>',
		type: 'POST',
		data: '',
		success: function(result) {
			if(!result.startsWith('failure ')){
				username = result;
				sessionStorage.username = username;
				// now we get the public key
				$.ajax({
					url: '/api/getpublickey.php?auth=<?=$_SESSION['auth_random']?>',
					type: 'POST',
					data: {
						username: username
					},
					success: function(result) {
						if(!result.startsWith('failure ')){
							pub_dbe = result;
							pub_pl = json_decompress(JSON.parse(pub_dbe));
							sessionStorage.public_key = JSON.stringify(pub_pl);
							ecdh_import(pub_pl, 0).then(function(r){
								pub_ck = r;
								// get master key and decrypt it with password key
								$.ajax({
									url: '/api/getmasterkey.php?auth=<?=$_SESSION['auth_random']?>',
									type: 'POST',
									data: '',
									success: function(result) {
										if(!result.startsWith('failure ')){
											mk_edb = JSON.parse(result);
											var password_key = JSON.parse(sessionStorage.passwordKey);
											//sessionStorage.passwordKey = random_characters(128); // ------------ security issue?
											// import password key as cryptokey and unset it from localStorage
											aes_import(password_key).then(function(re){
												// decrypt the masterkey with the password
												aes_decrypt(re, mk_edb).then(function(re){
													// decompress decrypted key
													mk_pl = json_decompress(re);
													sessionStorage.master_key = JSON.stringify(mk_pl);
													// import it as a cryptoKey object
													aes_import(mk_pl).then(function(re){
														mk_ck = re;
														
														// now we can grab and decrypt our own private key
														
														$.ajax({
															url: '/api/getprivatekey.php?auth=<?=$_SESSION['auth_random']?>',
															type: 'POST',
															data: {},
															success: function(result) {
																// decrypt private key with master key
																priv_pl = json_decompress(result);
																aes_decrypt(mk_ck, priv_pl).then(function(result){
																	sessionStorage.private_key = JSON.stringify(result);
																	// all done with the keys, now we determine what tab
																	// the person was last on when they logged out of the
																	// app, so that way they can get back where they left off
																	window.location.href = 'messaging/index.php';
																	display_error('foobar');
																});
															}
														});
													});
												});
											});
										}else{
											display_error(result);
										}
									}
								});
							});
						}else{
							display_error(result);
						}
					}
				});
			}else{
				display_error(result);
			}
		}
	});
});

// error function
function display_error(message){
	if(typeof Android == 'object'){
		Android.showAlert(message);
	}else{
		document.getElementById('error_div').innerHTML = message;
		$('#error_div').animate({
			bottom: "0%"
		});
		setTimeout(function(){
			$('#error_div').animate({
				bottom: "-200%"
			});
		}, 8500);
	}
}