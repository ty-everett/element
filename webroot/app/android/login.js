<?php
require_once('/srv/http/element.tk/security/check.php');
?>
// Element Network Client

// define global variables
var recoveryPhrase = "";
var recoveryKey = "";
var publicKey = "";
var privateKey = "";
var masterKey = "";
var username = "";
var phoneNumber = "";
var email = "";
var password = "";
var isEmail = true;
var isAnimationComplete = false;
var redirectURL = 'none';
var isLoadingShowing = false;

$(document).ready(function(){
	
	var test = document.getElementById('login_username').value;
	if(test.length > 1){
		$('#login_div').slideToggle();
	}
	
	$('#login_div').hide();
	$('#signup_div').hide();
	$('#recovery_key_container').hide();
	
	$('#login_form').on('submit', function(ev) {
		ev.preventDefault();
		var test = document.getElementById('login_username').value;
		if(test.length < 1){
			$('#login_div').slideToggle();
		}else{
			login_submit();
		}
	});
	$('#signup_form').on('submit', function(ev){
		ev.preventDefault();
		var test = document.getElementById('signup_username').value;
		if(test.length < 1){
			$('#signup_div').slideToggle();
		}else{
			signup_submit();
		}
	});
	
	$(document).keyup(function(e) {
		if (e.keyCode == 27) { // escape key maps to keycode `27`
			$('#recovery_key_container').fadeOut();
		}
	});
	$('#recovery_verify_text').keyup(function(e) {
		if (e.keyCode == 13) { // escape key maps to keycode `27`
			$('#recovery_verify').click();
		}
	});
	
	$('#recovery_key_generator_background').on('click', function(){
		$('#recovery_key_container').fadeOut();
	});
	
	$('#recovery_cancel').on('click', function(){
		$('#recovery_key_container').fadeOut();
	});
	
	$('#recovery_next').on('click', function(){
		$('#recovery_key_generator').animate({
			left: '-200%'
		}, 600);
		$('#recovery_key_generator').fadeOut();
		$('#recovery_key_verify').fadeIn();
		$('#recovery_key_verify').animate({
			left: '5%'
		}, 600);
	});
	
	$('#recovery_back').on('click', function(){
		$('#recovery_key_verify').animate({
			left: '200%'
		}, 600);
		$('#recovery_key_verify').fadeOut();
		$('#recovery_key_generator').fadeIn();
		$('#recovery_key_generator').animate({
			left: '5%'
		}, 600);
	});
	
	$('#recovery_verify').on('click', function(){
		if(document.getElementById('recovery_verify_text').value.trim().toLowerCase() == recoveryPhrase){
			
			// proceed to sign up
			
			// first we need to let the user know it's loading
			show_loading();
			
			/* variable names used 
			mk masterkey
			r recoverykey
			pw password
			pub publickey
			priv privatekey
			
			_ck cryptokey (imported or generated)
			_db compressed (json_compress)
			_pl plaintext (exported)
			_edb encrypted database (compressed, encrypted)
			*/
			
			// generate a master key
			aes_generate().then(function(mk_ck){
			 // export masterkey as plaintext
			 aes_export(mk_ck).then(function(mk_pl){
			  // compress masterkey for DB entry
			  mk_db = json_compress(mk_pl);
			  //alert("new AES masterkey: " + mk_db);
			  // derive cryptokey from password
			  aes_derive(password).then(function(pw_ck){
			   // encrypt masterkey with password for storage in DB
			   aes_encrypt(pw_ck, mk_db).then(function(mk_edb){
				//alert("encrypted new AES masterkey (into the DB): " + mk_edb);
				// derive cryptokey from recovery phrase
				aes_derive(recoveryPhrase).then(function(r_ck){
				 // encrypt masterkey with recoverykey for storage in DB
				 aes_encrypt(r_ck, mk_db).then(function(r_edb){
				   // generate an ECDH keypair
				   ecdh_generate().then(function(ecdh_keypair){
					// export the ECDH public key and compress it for DB
					ecdh_export(ecdh_keypair.publicKey).then(function(pub_db){
					 pub_db = json_compress(pub_db);
					 // export private ECDH key, compress, encrypt w/ master
					 ecdh_export(ecdh_keypair.privateKey).then(function(priv_db){
					  priv_db = json_compress(priv_db);
					  aes_encrypt(mk_ck, priv_db).then(function(priv_edb){
						// now we use ajax to submit all this to the database
						if(isEmail){
							$.ajax({
								url: '/api/register.php',
								type: 'POST',
								data: {
									publickey: quote(pub_db),
									privatekey: quote(priv_edb),
									masterkey: quote(mk_edb),
									recoverykey: quote(r_edb),
									password: sha512(password),
									username: username,
									email: sha512(email)
								},
								success: function(result) {
									// evaluate response
									if(result == 'success'){
										if(isAnimationComplete) {
											save_and_redirect('welcome.php');
										}else{
											redirectURL = 'welcome.php';
										}
									}else{
										display_error(result);
									}
								}
							});
						}else{
							$.ajax({
								url: '/api/register.php',
								type: 'POST',
								data: {
									publickey: quote(pub_db),
									privatekey: quote(priv_edb),
									masterkey: quote(mk_edb),
									recoverykey: quote(r_edb),
									password: sha512(password),
									username: username,
									phone: sha512(phoneNumber)
								},
								success: function(result) {
									// evaluate response
									if(result == 'success'){
										if(isAnimationComplete) {
											save_and_redirect('welcome.php');
										}else{
											redirectURL = 'welcome.php';
										}
									}else{
										display_error(result);
									}
								}
							});
						}
						  
						  
					  });
					 });
					});
				   });
				  });
				});
			   });
			  });
			 });
			});
			
		}else{
			display_error("Recovery phrase incorrect.");
		}
	});
	
});

// signup form submission function
function signup_submit(){
	// get the data
	username = document.getElementById('signup_username').value;
	password = document.getElementById('signup_password').value;
	var emailorphone = document.getElementById('signup_emailorphone').value;
	var passwordConfirm = document.getElementById('signup_passwordconfirm').value;
	
	// check passwords match
	if(password != passwordConfirm){
		display_error('Passwords do not match.');
		return;
	}
	
	// check password length
	if(password.length < 12){
		display_error('Your password should be at least 12 characters. Please make sure to choose a long, secure password with lots of numbers, letters, and special characters!');
		return;
	}
	
	// verify password capital letter
	if(!(/[A-Z]/.test(password))){
		display_error('Your password needs a capital letter. Please make sure to choose a long, secure password with lots of numbers, letters, and special characters!');
	}
	
	// verify password lowercase letter
	if(!(/[a-z]/.test(password))){
		display_error('Your password needs a lowercase letter. Please make sure to choose a long, secure password with lots of numbers, letters, and special characters!');
	}
	
	// verify password number
	if(!(/[0-9]/.test(password))){
		display_error('Your password needs a number. Please make sure to choose a long, secure password with lots of numbers, letters, and special characters!');
	}
	
	// check username length
	if(username.length > 20){
		display_error("Your username can't be longer than 20 characters!");
		return;
	}
	
	// determine if it's a phone number or an email address
	var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	if(re.test(emailorphone)){
		// email address
		isEmail = true;
		email = emailorphone.trim();
	}else{
		// check if a phone number
		var re = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im;
		if(re.test(emailorphone)){
			isEmail = false;
			phoneNumber = emailorphone.trim();
			phoneNumber = phoneNumber.replace(/\D/g,'');
		}else{
			display_error('Please enter either a valid phone numer or a correct email address.');
			return;
		}
	}
	
	// generate recovery key and hash it
	if(recoveryPhrase.length < 1){
		recoveryPhrase = generate_recovery_phrase();
		document.getElementById('recovery_key').innerHTML = recoveryPhrase;
	}
	$('#recovery_key_container').fadeIn();
	$('#recovery_next').focus();
	
	///////////////////////      NOTE
	
	// from here on out we handle the rest from recovery_verify.click
	
	///////////////////////      NOTE
	
}

// login form submission function
function login_submit(){
	// start animation for loading
	show_loading();
	
	// get username and password
	username = document.getElementById('login_username').value;
	password = document.getElementById('login_password').value;
	
	// determine email, username, or phone number
	var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	if(re.test(username)){
		username = username.trim();
		
		// continue login with email address
		
		$.ajax({
			url: '/api/login.php',
			type: 'POST',
			data: {
				email: sha512(username),
				password: sha512(password)
			},
			success: function(result) {
				// evaluate response
				if(result == 'success'){
					if(isAnimationComplete){
						save_and_redirect('login.php');
					}else{
						redirectURL = 'login.php';
					}
				}else{
					display_error(result);
				}
			}
		});
		
	}else{
		var re = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im;
		if(re.test(username)){
			username = username.trim().replace(/\D/g,'');
			
			// continue login with phone number
			
			$.ajax({
				url: '/api/login.php',
				type: 'POST',
				data: {
					phone: sha512(username),
					password: sha512(password)
				},
				success: function(result) {
					// evaluate response
					if(result == 'success'){
					if(isAnimationComplete){
						save_and_redirect('login.php');
					}else{
						redirectURL = 'login.php';
					}
					}else{
						display_error(result);
					}
				}
			});
			
		}else{
			username = username.trim();
			
			// continue login with username
			
			$.ajax({
				url: '/api/login.php',
				type: 'POST',
				data: {
					username: username,
					password: sha512(password)
				},
				success: function(result) {
					// evaluate response
					if(result == 'success'){
					if(isAnimationComplete){
						save_and_redirect('login.php');
					}else{
						redirectURL = 'login.php';
					}
					}else{
						display_error(result);
					}
				}
			});
			
		}
	}
	
}

// loading show / hide
function show_loading(){
	if(isLoadingShowing == false){
		isLoadingShowing = true;
		$('#recovery_key_container').fadeOut("fast", function() {
			if(isLoadingShowing == false){return;}
			$("html, body").animate({ scrollTop: 0 }, "fast");
			var img = document.getElementById('splashimage');
			var height = img.clientHeight;
			var h = (($(window).height() / 2) - ( height / 2 ));
			$( "#splashimage" ).animate({
				top: h
			}, 800, function() {
				if(isLoadingShowing == false){return;}
				setTimeout(function(){
					$('#splashimage').animate({
						left: '-200%'
					}, 600, function(){
						isAnimationComplete = true;
						if(redirectURL != 'none'){
							save_and_redirect(redirectURL);
						}
					});
				}, 1100);
			});
			$('#content_container').animate({
				top: $(window).height()
			}, 800);
		});
	}
}

function hide_loading(){
	if(isLoadingShowing){
		isLoadingShowing = false;
		$('#splashimage').animate({
			left: 0
		}, 800);
		$('#splashimage').animate({
			top: 0
		}, 800);
		$('#content_container').animate({
			top: 0
		}, 800);
	}
}

// error function
function display_error(message){
	$('#recovery_key_container').fadeOut();
	hide_loading();
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

// mnemonic seed generator
function generate_recovery_phrase(){
	var m = new Mnemonic(128);
    var r = m.toWords();
	var v = "";
	for(var i = 0; i < r.length; i++){
		v += r[i] + ' ';
	}
	return v.trim().toLowerCase();
}

// back function for Android
var Android_back_pressed = function(){
	if ($("#recovery_key_container").is(":visible")) {
        $('#recovery_key_container').fadeOut();
    }
}

// saving and redirecting with sessionStorage
function save_and_redirect(url){
	aes_derive(password).then(function(result){
		aes_export(result).then(function(finalkey){
			finalkey = JSON.stringify(finalkey);
			sessionStorage.passwordKey = finalkey;
			window.location.href = url;
		});
	});
}