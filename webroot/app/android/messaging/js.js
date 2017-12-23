<?php
require_once('/srv/http/element.tk/security/check.php');
?>
var friends_ringsigs = {};
var ui_state = 'contacts';

function everything_loaded(){
	
	// Element Network Messaging Client
	
	// hide hidden components
	$('.messages').hide();
	// hide back button
	// hide contact info button
	$('#contactslabel').hide();
	$('#sendrequest').hide();
	$('#acceptrequest').hide();
	$('#conversation').hide();
	
	// set selected contact as being none to start
	var selected_contact = 'none';
	
	// let's get a list of friends
	$.ajax({
		url: '/api/messaging/getfriends.php?auth=<?=$_SESSION['auth_random']?>',
		type: 'POST',
		data: '',
		success: function(result) {
			if(result != 'none'){
				var names = result.split("[Delimiator0]");
				for(var i = 0; i < names.length-1; i++){
					get_friends_execute(names, i);
				}
			}else{
				document.getElementById('newcontactslist').innerHTML = "";
				$('#contactslabel').hide();
			}
		}
	});
	
	function get_friends_execute(names, i){
		ringsig_decrypt(priv_ck, names[i]).then(function(result){
			friends_ringsigs[result] = names[i];
			document.getElementById('contactslist').innerHTML += contactify(result);
		});
	}
	
	// get list of new contact requests
	$.ajax({
		url: '/api/messaging/getnewfriends.php?auth=<?=$_SESSION['auth_random']?>',
		type: 'POST',
		data: '',
		success: function(result) {
			if(result != 'none'){
				var names = result.split("[Delimiator0]");
				for(var i = 0; i < names.length-1; i++){
					get_new_friends_execute(names, i);
				}
			}else{
				document.getElementById('newcontactslist').innerHTML = "";
				$('#contactslabel').hide();
			}
		}
	});
	
	function get_new_friends_execute(names, i){
		ringsig_decrypt(priv_ck, names[i]).then(function(result){
			friends_ringsigs[result] = names[i];
			document.getElementById('newcontactslist').innerHTML += contactify(result);
			$('#contactslabel').show();
		});
	}
	
	// contact search function
	$('#searchtext').on('input', function(){
		if(($('#searchtext').val().trim()).length > 3){
			$.ajax({
				url: '/api/search.php?auth=<?=$_SESSION['auth_random']?>',
				type: 'POST',
				data: {query: $('#searchtext').val()},
				success: function(result) {
					if(result == 'No results'){
						document.getElementById('searchlist').innerHTML = '<p class="UISubtext UICenterHorizontal">No results</p>';
					}else{
						var names = result.split(",");
						var output = "";
						for(var i = 0; i < names.length-1; i++){
							contactname = names[i];
							output += contactify(contactname);
						}
						document.getElementById('searchlist').innerHTML = '<p class="UISubtext UICenterHorizontal" style="marin-bottom:0px;padding-bottom:0px;">Search Results</p>' + output;
					}
				}
			});
		}else if(($('#searchtext').val().trim()).length > 0){
			document.getElementById('searchlist').innerHTML = '<p class="UISubtext UICenterHorizontal">Keep typing to see results...</p>';
		}else{
			document.getElementById('searchlist').innerHTML = '';
		}
	});
	
	// contact printing
	function contactify(name){
		return '<button class="contact"><p>' + name + '</p></button>';
	}
	
	function toggle_ui(){
		if(ui_state == 'contacts'){
			ui_state = 'convo';
			// set to convo
			$('.content').hide("slide", { direction: "left" }, 400);
			$('.bottomtabs').hide("slide", { direction: "left" }, 400);
			$('.main').text(selected_contact);
			$('.messages').show("slide", { direction: "right" }, 400);
			// show back button
			// show contact info button
			// hide search button
		}else{
			ui_state = 'contacts';
			// set to contacts;
			$('.messages').hide("slide", { direction: "right" }, 400);
			$('.content').show("slide", { direction: "right" }, 400);
			$('.bottomtabs').show("slide", { direction: "right" }, 400);
			$('.main').text('Secure Messaging');
			$('#sendrequest').hide();
			$('#acceptrequest').hide();
			$('#conversation').hide();
			// show back button
			// show contact info button
			// hide search button
			
		}
	}
	
	function select_contact(name){
		selected_contact = name;
		toggle_ui();
		
		if(typeof friends_ringsigs[name] == 'string'){
			$.ajax({
				url: '/api/messaging/getstatus.php?auth=<?=$_SESSION['auth_random']?>',
				type: 'POST',
				data: {ringsig: friends_ringsigs[name]},
				success: function(result) {
					if(result < 0){ // server doesn't know anything
						$('#sendrequest').show();
					}else if(result == 0){ // pending request
						$('#acceptrequest').show();
					}else if(result == 1){ // normal contact, show messaging
						$('#conversation').show();
					}
					// console.log("result: " + result); // DEBUG
				}
			});
		}else{ // we didn't have the ringsig so obviously just send request screen
			$('#sendrequest').show();
		}
	}
	
	
	// contact request function
	function send_contact_request(name){
		
		// we need to get some random public keys of actual users from the server
		
		$.ajax({
			url: '/api/getringkeys.php?auth=<?=$_SESSION['auth_random']?>',
			type: 'POST',
			data: '',
			success: function(result) {
				var keys = result.split('[Delimiator]');
				var ringsig = get_ringsig_random(keys, 5, quote(sessionStorage.public_key));
				// now we have the ringsig, we just need to add it to the database
				
				// generate authorization tokens and hashes
				var auth_token = random_characters(128);
				var auth_hash = sha512(auth_token);
				aes_encrypt(mk_ck, auth_token).then(function(result){
					var auth_content = result;
					
					// encrypt the content with our private key to their public key
					$.ajax({
						url: '/api/getpublickey.php?auth=<?=$_SESSION['auth_random']?>',
						type: 'POST',
						data: {
							username: name
						},
						success: function(result) {
							ecdh_import(json_decompress(JSON.parse(result))).then(function(result){
								var rcpt_pk = result;
								ecdh_derive(rcpt_pk, priv_ck).then(function(result){
									aes_encrypt(result, sessionStorage.username).then(function(result){
										var content = ringsig + quote(result);
										// build and send
										$.ajax({
											url: '/api/messaging/sendfriendrequest.php?auth=<?=$_SESSION['auth_random']?>',
											type: 'POST',
											data: {
												username: name,
												content: content,
												authhash: auth_hash,
												authcontent: quote(auth_content),
												profilekeys: ''
											},
											success: function(result) {
												
												
												// indicate request sent
												toggle_ui();
												
											}
										});
									});
								});
							});
						}
					});
				});
			}
		});
	}
	
	function accept_friend_request(name){
		
		
		// we need to get some random public keys of actual users from the server
		
		$.ajax({
			url: '/api/getringkeys.php?auth=<?=$_SESSION['auth_random']?>',
			type: 'POST',
			data: '',
			success: function(result) {
				var keys = result.split('[Delimiator]');
				var ringsig = get_ringsig_random(keys, 5, quote(sessionStorage.public_key));
				// now we have the ringsig, we just need to add it to the database
				
				// generate authorization tokens and hashes
				var auth_token = random_characters(128);
				var auth_hash = sha512(auth_token);
				aes_encrypt(mk_ck, auth_token).then(function(result){
					var auth_content = result;
					
					// encrypt the content with our private key to their public key
					$.ajax({
						url: '/api/getpublickey.php?auth=<?=$_SESSION['auth_random']?>',
						type: 'POST',
						data: {
							username: name
						},
						success: function(result) {
							ecdh_import(json_decompress(JSON.parse(result))).then(function(result){
								var rcpt_pk = result;
								ecdh_derive(rcpt_pk, priv_ck).then(function(result){
									aes_encrypt(result, sessionStorage.username).then(function(result){
										var content = ringsig + quote(result);
										// build and send
										$.ajax({
											url: '/api/messaging/acceptfriendrequest.php?auth=<?=$_SESSION['auth_random']?>',
											type: 'POST',
											data: {
												username: name,
												content: content,
												authhash: auth_hash,
												authcontent: quote(auth_content),
												contentlookup: friends_ringsigs[name],
												profilekeys: ''
											},
											success: function(result) {
												
												// alert(result); // DEBUG
												// TODO indicate request sent
												// go to messaging with this person.
												$('#acceptrequest').hide();
												$('#conversation').show();
												
											}
										});
									});
								});
							});
						}
					});
				});
			}
		});
		
		
	}
	
	function decline_friend_request(name){
		
		$.ajax({
			url: '/api/messaging/declinefriendrequest.php?auth=<?=$_SESSION['auth_random']?>',
			type: 'POST',
			data: {
				content: friends_ringsigs[name]
			},
			success: function(result) {
				
				
				// TODO indicate request sent
				// go back to main screen, refreshing page
				toggle_ui();
				
			}
		});
		
	}
	
	$('body').on('click', '.contact', function() {
    select_contact($(this).text());
	});
	
	$('body').on('click', '#sendrequest_button', function() {
		if(selected_contact != 'none'){
			send_contact_request(selected_contact);
		}
	});
	
	$('body').on('click', '#acceptrequest_accept', function() {
		if(selected_contact != 'none'){
			accept_friend_request(selected_contact);
		}
	});
	
	$('body').on('click', '#acceptrequest_decline', function() {
		if(selected_contact != 'none'){
			decline_friend_request(selected_contact);
		}
	});
	
}