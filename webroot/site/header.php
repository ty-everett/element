<?php
require_once('/srv/http/element.tk/security/check.php');
// header printing function
function printHeader($title){
	include('../snippets/HTMLTop.php');
	echo'<title>'.$title.'</title>
<link rel="stylesheet" tyle="text/css" href="stylesheet.css" />
<script src="/js/jquery.js"></script>
</head>
<body>
<div class="navigation">
<a href="index.php">Home</a>
<a href="video.php">Video</a>
<a href="https://tyweb.us/contact/">Contact</a>
<a href="tech.php">Technical Blog</a>
</div>';
}