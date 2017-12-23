<?php
require_once('/srv/http/element.tk/security/check.php');
include('/srv/http/element.tk/snippets/HTMLTop.php');
include('header.php');
require_login();
?>
<title>Logging In...</title>
<style>
body {
	background-color:#440044;
	color:yellow;
	font-family:helvetica;
}
h1 {
	padding-top:2em;
}
</style>
<script src="/js/jquery.js"></script>
<script src="/js/cryptofunctions.js"></script>
<script src="getinfo.js"></script>
</head>
<body>
<center>
<h1>Logging In...</h1>
</center>
</body>
</html>