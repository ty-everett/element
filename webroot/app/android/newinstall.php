<?php
require_once('/srv/http/element.tk/security/check.php');
// Element Network Client Authentication

// if already logged in, redirect to messages
if($_SESSION['isLoggedIn'] == 1){
	header('location: login.php');
	die();
}

include('/srv/http/element.tk/snippets/HTMLTop.php');
?>
<title>Element</title>
<link rel="stylesheet" href="login.css" />
<script src="/js/jquery.js"></script>
<script src="/js/mnemonic.js"></script>
<script src="/js/sha512.js"></script>
<script src="/js/cryptofunctions.js"></script>
<script src="login.js"></script>
</head>
<body>
<center>
<img id="splashimage" src="img/logo.png" />
<div id="content_container">
<h2 >Element Communications</h2>
<h3>Privacy. Productivity. Power.</h3>
<form id="login_form" method="POST" action="/api/login.php">
<div id="login_div">
<input type="text" class="UITextField" id="login_username" name="username" placeholder="Phone, email, or username" /><br />
<input type="password" class="UITextField" id="login_password" name="password" placeholder="Password" /><br />
</div>
<input type="submit" class="UIButton" id="login_button" value="Log In" />
</form>
<form id="signup_form" method="POST" action="/api/register.php">
<div id="signup_div">
<input type="text" class="UITextField" id="signup_username" name="username" placeholder="Username" /><br />
<input type="password" class="UITextField" id="signup_password" name="password" placeholder="Password" /><br />
<input type="password" class="UITextField" id="signup_passwordconfirm" placeholder="Confirm" /><br />
<input type="text" class="UITextField" id="signup_emailorphone" name="emailorphone" placeholder="Email or Phone" /><br />
</div>
<input type="submit" class="UIButton" id="signup_button" value="Sign Up" />
</form>
<div id="footer">
<p>Copyright &copy 2017 Element Communications, all rights reserved.</p>
<p><a href="/site/terms.php">Terms of Service</a> | <a href="/site/privacy.php">Privacy Policy</a>
</div>
</div>
</center>
<div id="error_div"></div>
<div id="recovery_key_container">
<div id="recovery_key_generator_background"></div>
<div id="recovery_key_generator">
<h2>Your Recovery Porase</h2>
<p>Your recovery phrase is used in the event that you lose your password, to help you get
back into your account. Make note of it, and keep it in a secure location.</p>
<p style="color:red;font-weight:bold;text-transform:uppercase;">Never share your recovery
phrase with anyone!</p>
<center>
<div id="recovery_key"></div>
</center>
<p>Make note of your recovery phrase now. On the next screen, you'll be asked to provide it.
If you lose your recovery phrase, you'll lose access to your account!</p>
<button id="recovery_cancel">Cancel</button>
<button id="recovery_next">Next</button>
</div>
<div id="recovery_key_verify">
<h2>Verify Recovery Phrase</h2>
<p>To make sure you saved your recovery phrase, please enter it below.
If you didn't save it in a secure location, please go back and do so.</p>
<center>
<textarea id="recovery_verify_text" placeholder="Enter recovery phrase...">
</textarea>
</center>
<button id="recovery_back">Back</button>
<button id="recovery_verify">Verify</button>
</div>
</div>
</body>
</html>