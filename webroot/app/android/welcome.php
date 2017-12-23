<?php
require_once('/srv/http/element.tk/security/check.php');
// Element Network Newbie Greeter

// include API header
include('../../api/header.php');

requireLogin();

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Element</title>
<link rel="stylesheet" href="/css/normalize.css" />
<link rel="stylesheet" href="/js/jqueryui/jquery-ui.min.css" />
<link rel="stylesheet" href="/js/jqueryui/jquery-ui.structure.min.css" />
<link rel="stylesheet" href="/js/jqueryui/jquery-ui.theme.min.css" />
<style>
body {
	margin:0px;
	padding:0px;
	color:yellow;
	background-color:#440044;
	font-family:helvetica;
}
.container {
	left:5%;
	position:absolute;
	top:0%;
	width:90%;
}
#buttonbar {
	position:fixed;
	bottom:0px;
	width:100%;
	background-color:#440044;
	z-index:2;
	border-top:1px solid yellow;
}
.UIButton {
	width:40%;
	display:inline;
	color:#220022;
	background-color:yellow;
	border-radius:0.25em;
	border:2px solid #220022;
	font-size:1.2em;
	margin-top:0.5em;
	margin-bottom:0.5em;
	text-transform:uppercase;
	font-weight:bold;
	outline:none;
	text-decoration:none;
}
.UITextField {
	margin:0.5em;
	padding:0.1em;
	border-radius:0.1em;
	border:1px solid #cccc00;
	background-color:white;
	outline:none;
	width:80%;
	font-size:1.2em;
}
.terms {
	width:90%;
	background-color:white;
	color:black;
	border:1px solid yellow;
	align:left;
	text-align:left;
	padding:1em;
	margin-bottom:1em;
}
#button_back{
	margin-left:5%;
	display:none;
}
#button_next {
	margin-right:10%;
	float:right;
	width:80%;
}
#error_div {
	position:fixed;
	width:100%;
	background-color:orange;
	border-top:2px solid #220022;
	color:#220022;
	padding:1em;
	padding-left:0em;
	padding-right:0em;
	margin:0px;
	left:0px;
	bottom:-200%;
	text-align:center;
	z-index:2;
}
.logo {
	width:50%;
}
#profile_photo {
	opacity:0;
	outline:none;
	position:absolute;
	width:0.5px;
	height:0.5px;
}
#profile_photo_display {
	width:80%;
}
::-webkit-scrollbar {
    width: 0px;  /* remove scrollbar space */
    background: transparent;  /* optional: just make scrollbar invisible */
}
</style>
<script src="/js/jquery.js"></script>
<script src="/js/jqueryui/jquery-ui.min.js"></script>
<script src="/js/jqueryui/punch.js"></script>
<script>
var currentDisplay = 1; // set current screen to be 1
$(document).ready(function(){
	
	// hide invisible components
	$('#button_back').hide();
	$('#c2').hide();
	$('#c3').hide();
	$('#c4').hide();
	$('#c5').hide();
	$('#c6').hide();
	$('#c7').hide();
	$('#c8').hide();
	
	// fade in on load
	var visible = $("*:visible");
	visible.hide();
	visible.fadeIn(800);
	
	// profile photo uploader stuff
	$('#profile_photo_display_container').hide();
	$('#uploading').hide();
	$('#profile_photo').on('change', function(){
		var the_file = this.files[0];
		if (this.files && this.files[0]) {
			$('#uploading').fadeIn();
			$('#buttonbar').fadeOut();
			var reader = new FileReader();
			reader.onload = function (e) {
				$('#profile_photo_display').attr('src', e.target.result);
				$('#profile_photo_display_container').fadeIn();
				// now we upload it to the server (unencrypted, they are public).
				$.ajax({
					url: '/api/profile/setprofilephoto.php?auth=<?=$_SESSION['auth_random']?>',
					type: 'POST',
					data: {
						photo: the_file.value
					},
					success: function(result) {
						// evaluate response
						if(result != 'success'){
							display_error(result);
						}
						// now it's uploaded, hide the loading screen
						$('#uploading').fadeOut();
						$('#buttonbar').fadeIn();
					},
					error: function(error){
						$('#uploading').fadeOut();
						$('#buttonbar').fadeIn();
						display_error(error);
					}
				});
			}
			reader.readAsDataURL(this.files[0]);
		}else{
			display_error('Please select a propper image file for upload.');
		}
	});
	
	// apply UI themes
	$(".checkbox_radio").checkboxradio();
	
	// change button between skip and next
	$('#profile_name').on('input', function(){
		if($('#profile_name').val().length < 1){
			$('#button_next').text('Skip');
		}else{
			$('#button_next').text('Next');
		}
	});
	$('#profile_tagline').on('input', function(){
		if($('#profile_tagline').val().length < 1){
			$('#button_next').text('Skip');
		}else{
			$('#button_next').text('Next');
		}
	});
	$('#done_button').on('click', function(){
		$('*').fadeOut(1200);
		setTimeout(function(){
			window.location = 'login.php';
		}, 1200);
	});
	
	// enter for name and tagline
	$('#profile_name').keyup(function(e) {
		if (e.keyCode == 13) {
			$('#button_next').click();
		}
	});
	$('#profile_tagline').keyup(function(e) {
		if (e.keyCode == 13) {
			$('#button_next').click();
		}
	});
	
	// advance and go back between screens
	$('#button_next').on('click', function(){
		currentDisplay++;
		if(currentDisplay > 1){
			$('#button_next').animate({
				width: '40%',
				MarginRight: '5%'
			}, 400, function(){$('#button_back').fadeIn();});
			if(currentDisplay > 3 || currentDisplay < 2){
				$('#button_next').text('Next');
				$('#button_back').text('Back');
			}else{
				$('#button_next').text('Agree');
				$('#button_back').text('Disagree');
			}
		}
		$('#c' + (currentDisplay - 1)).hide("slide", { direction: "left" }, 400);
		$('#c' + currentDisplay).show("slide", { direction: "right" }, 400);
		updateUI(currentDisplay);
	});
	$('#button_back').on('click', function(){
		currentDisplay--;
		if(currentDisplay < 2){
			currentDisplay = 1;
			$('#button_back').fadeOut(400, function(){
					$('#button_next').animate({
					width: '80%',
					MarginRight: '10%'
				}, 400);
			});
		}
		if(currentDisplay > 3 || currentDisplay < 2){
			$('#button_next').text('Next');
			$('#button_back').text('Back');
		}else{
			$('#button_next').text('Agree');
			$('#button_back').text('Disagree');
		}
		$('#c' + (currentDisplay + 1)).hide("slide", { direction: "right" }, 400);
		$('#c' + currentDisplay).show("slide", { direction: "left" }, 400);
		updateUI(currentDisplay);
	});
	
	// update the interface, submit forms and handle errors as needed
	var updateUI = function(val){
		if(val == 3){ // when user agrees with terms
			$.ajax({
				url: '/api/acceptterms.php?auth=<?=$_SESSION['auth_random']?>',
				type: 'POST',
				data: {},
				success: function(result) {
					// evaluate response
					if(result != 'success'){
						display_error(result);
					}
				}
			});
		}else if(val == 4){ // when user agrees with privacy pollicy
			$.ajax({
				url: '/api/acceptprivacy.php?auth=<?=$_SESSION['auth_random']?>',
				type: 'POST',
				data: {},
				success: function(result) {
					// evaluate response
					if(result != 'success'){
						display_error(result);
					}
				}
			});
		}else if(val == 5){ // name entry (and account type submission)
			$('#button_next').text('Skip');
			isPublicChecked = $('#radio-1:checked').val();
			isPrivateChecked = $('#radio-2:checked').val();
			if(isPrivateChecked == 'on'){
				accountType = 'private';
			}else if(isPublicChecked == 'on'){
				accountType = 'public';
			}else{
				display_error('You need to pick either public or private for your account type!');
				return;
			}
			$.ajax({
				url: '/api/profile/setaccounttype.php?auth=<?=$_SESSION['auth_random']?>',
				type: 'POST',
				data: {
					type: accountType
				},
				success: function(result) {
					// evaluate response
					if(result != 'success'){
						display_error(result);
					}
				}
			});
		}else if(val == 6){ // tagline entry (and name submission)
			$('#button_next').text('Skip');
			
			var name = $('#profile_name').val().trim();
			if(name.length > 40){
				display_error('Your name can\'t be longer than 40 characters!');
			}
			
			if(name.length < 1 || name == null){
				return;
			}
			
			$.ajax({
				url: '/api/profile/setaccountname.php?auth=<?=$_SESSION['auth_random']?>',
				type: 'POST',
				data: {
					name: name
				},
				success: function(result) {
					// evaluate response
					if(result != 'success'){
						display_error(result);
					}
				}
			});
			
		}else if(val == 7){ // profile photo upload (and tagline validation)
			$('#buttonbar').show("slide", { direction: "up" }, 400);
			$('#button_next').text('Skip');
			
			var tagline = $('#profile_tagline').val().trim();
			if(tagline.length > 140){
				display_error('Your tagline can\'t be longer than 140 characters! Keep it short, simple, and to-the-point.');
			}
			
			if(tagline.length < 1 || tagline == null){
				return;
			}
			
			$.ajax({
				url: '/api/profile/setaccounttagline.php?auth=<?=$_SESSION['auth_random']?>',
				type: 'POST',
				data: {
					tagline: tagline
				},
				success: function(result) {
					// evaluate response
					if(result != 'success'){
						display_error(result);
					}
				}
			});
			
		}else if(val == 8){ // final screen (and profile photo upload
			$('#buttonbar').hide("slide", { direction: "down" }, 1200);
			var img = document.getElementById('c8');
			var height = img.clientHeight;
			var h = (($(window).height() / 2) - ( height / 2 ));
			$( "#c8" ).animate({
				top: h
			}, 800);
		}
	}
	
	// error function
	function display_error(message){
		$('#button_back').click();
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
			}, 5000);
		}
	}
});
var Android_back_pressed = function(){
	if ($("#button_back").is(":visible")) {
        $('#button_back').click();
    }
}
</script>
</head>
<body>
<div class="container" id="c1">
<center>
<h1>Welcome</h1>
<p>Welcome to Element! You're now part of the most secure social network
in the world. That means you can have peace of mind your messages aren't being
spied on, your usage habits aren't sold to advertisers, and that you are in
control of your privacy.</p>
</center>
</div>
<div class="container" id="c2">
<center>
<h1>Content Agreement</h1>
<p>Please read and agree to this so you know what you're allowed to post here.</p>
<div class="terms">
<?php
echo(file_get_contents('/srv/http/element.tk/terms.txt'));
?>
</div>
</center>
</div>
<div class="container" id="c3">
<center>
<h1>Privacy Agreement</h1>
<p>Have a look at all the ways we don't/can't/won't spy on you</p>
<div class="terms">
<?php
echo(file_get_contents('/srv/http/element.tk/privacy.txt'));
?>
</div>
</center>
</div>
<div class="container" id="c4">
<center>
<h1>You're in Control</h1>
<p>Let's set things up the way <b><i><u>you</b></i></u> want.</p>
<fieldset>
    <legend>Account Type: </legend>
    <label for="radio-1">Public</label>
    <input type="radio" name="radio-1" id="radio-1" class="checkbox_radio">
    <label for="radio-2">Private</label>
    <input type="radio" name="radio-1" id="radio-2" class="checkbox_radio">
</fieldset>
</center>
<p><b>Public:</b> Everything you post to your profile is visible to everyone.
All your messages will remain encrypted and inaccessible by prying eyes.</p>
<p><b>Private:</b> All information associated with your account is encrypted and
accessible only to people you choose. The only information that isn't private is
your name, profile photo, tagline and username.</p>
</div>
<div class="container" id="c5">
<center>
<h1>Your Name</h1>
<p>What can we call you?</p>
<input class="UITextField" type="text" id="profile_name" placeholder="Your name" />
</center>
<p>Everything beyond this point is completely optional. Your name,
profile picture, and tagline will be made public so people can find you if you provide them.</p>
</div>
<div class="container" id="c6">
<center>
<h1>Tagline</h1>
<p>How will your friends know it's you?</p>
<input class="UITextField" type="text" id="profile_tagline" placeholder="Your tagline" />
</center>
<p>A tagline is a quote which will distinguish you from everyone else. When people
see your tagline, they should know it's you.</p>
<p>For example, Element's tagline is "<b>Privacy. Productivity. Power.</b>"</p>
</div>
<div class="container" id="c7">
<center>
<h1>Profile Picture</h1>
<input type="file" id="profile_photo" />
<div id="profile_photo_display_container"><img src="#" id="profile_photo_display" /></div><br/>
<label for="profile_photo"><div class="UIButton" style="padding-0.3em;padding-left:0.5em;padding-right:0.5em;">Set Profile Photo</div></label>
<p id="uploading">Uploading, please wait...</p>
</center>
</div>
<div class="container" id="c8">
<center>
<img class="logo" src="img/logo.png" />
<p>You're all set!</p>
<button class="UIButton" id="done_button">Continue</button>
</center>
</div>
<div id="buttonbar">
<button class="UIButton" id="button_back">Back</button>
<button class="UIButton" id="button_next">Next</button>
</div>
<div id="error_div"></div>
</body>
</html>