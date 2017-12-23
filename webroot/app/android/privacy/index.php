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
	
	
	
}
</script>
</head>
<body>
<div class="header">
<h2 class="main">Your Privacy</h2>
</div>
<div class="content">
<p style="padding-top:4em;">We're working on bringing you the most customizable
privacy control panel of any platform on the internet. For now, you can rest
assured that all settings are set to their most secure values.</p>
</div>
<?php
print_footer('privacy');
?>