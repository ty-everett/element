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
<h2 class="main">Your Feed</h2>
</div>
<div class="content">
<p style="padding-top:4em;">This page is still being built. When it's done,
you'll be able to get all the content you care about in one place.</p>
</div>
<?php
print_footer('posts');
?>