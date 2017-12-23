<?php
require_once('/srv/http/element.tk/security/check.php');
include('../header.php');
require_login();
include('/srv/http/element.tk/snippets/HTMLTop.php');
?>
<title>Element</title>
<link rel="stylesheet" href="/js/jqueryui/jquery-ui.min.css" />
<link rel="stylesheet" href="/js/jqueryui/jquery-ui.structure.min.css" />
<link rel="stylesheet" href="/js/jqueryui/jquery-ui.theme.min.css" />
<link rel="stylesheet" type="text/css" href="../style.css" />
<script src="/js/jquery.js"></script>
<script src="/js/jqueryui/jquery-ui.min.js"></script>
<script src="/js/jqueryui/punch.js"></script>
<script src="/js/cryptofunctions.js"></script>
<script src="../importkeys.js"></script>
<script>
function everything_loaded(){
	
	// Element Network Profile Management Client
	
	// [!] ISSUE: If user has commas in keys or values it will royally
	//            fuck up everything by offsetting the whole thing by one
	//            value, thus associating the values as keys and the keys
	//            as values for each attribute of the array after the
	//            point at which the comma is offsetting the mapping.
	//            I'm lazy so I just assume users aren't fucktards and won't
	//            fix this for now, but it needs to be fixed in the future.
	
	// retrieve profile settings
	$.ajax ({
		url: '/api/getsettings.php?auth=<?=$_SESSION['auth_random']?>',
		type: 'POST',
		data: '',
		success: function(result){
			// decrypt the account settings with masterkey
			aes_decrypt(mk_ck, result).then(function(result){
				var account_settings = result;
				// now we parse the key value pairs into an array for use
				settings_parts = account_settings.split(',');
				// we need a new hash map to store stufff
				var settings_array = {};
				for(int i = 0; i < settings_parts; i+=2){
					settings_array[settings_parts[i]] = settings_parts[i+1];
				}
				// now we have associated a key-value pair with all settings and values
				// we just echo it out to the HTML below
				$('#name').val(settings_array['name']);
				$('#username').val(settings_array['username']);
				$('#tagline').val(settings_array['tagline']);
				$('#email').val(settings_array['email']);
				$('#phone').val(settings_array['phone']);
				$('#public').val(settings_array['public']);
			});
		});
	});
	
}
</script>
</head>
<body>
<div class="header">
<h2 class="main">Your Profile</h2>
</div>
<div class="content">
<p style="padding-top:4wem;">Here you'll be able to customize every aspect of
your account, view your data, and control your settings.</p>
<h1 id="name"></h1>
<h1 id="username"></h1>
<h1 id="tagline"></h1>
<h1 id="email"></h1>
<h1 id="phone"></h1>
<h1 id="public"></h1>
<h1 id="friends"></h1>
</div>
<?php
print_footer('profile');
?>