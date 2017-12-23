<?php
require_once('/srv/http/element.tk/security/check.php');
include('/srv/http/element.tk/snippets/HTMLTop.php');
?>
<title>Element</title>
<style>
body {
	background-color:#440044;
	color:yellow;
	font-family:helvetica;
	margin:0px;
	padding:0px;
}
.terms {
	border:1px solid yellow;
	background-color:white;
	color:black;
	padding:1em;
}
.agree {
	background-color:yellow;
	font-weight:bold;
	font-size:2em;
	color:#220022;
	width:80%;
	text-align:center;
	margin-top:0.5em;
	margin-bottom:0.5em;
	border-radius:0.3em;
	border:1px solid #330033;
}
</style>
</head>
<body>
<center>
<h2>Element Communications</h2>
<p>Privacy Is Our Pollicy</p>
</center>
<div class="terms">
<?php
echo(file_get_contents('privacy.txt'));
?>
</div>
</body>
</html>