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
<script src="/js/sha512.js"></script>
<script src="../importkeys.js"></script>
<script src="js.js"></script>
</head>
<body>
<div class="header">
<h2 class="main">Secure Messaging</h2>
</div>
<div class="content">
<div id="search">
<center>
<form id="searchform">
<input type="text" placeholder="Search..." id="searchtext" />
</form>
</center>
<div id="searchlist"></div></div>
<div id="newcontactslist">
<p class="UISubtext UICenterHorizontal">New friend requests</p></div>
<p id="contactslabel" class="UISubtext UICenterHorizontal">Your friends</p>
<div id="contactslist">
</div>
</div>
<div class="messages">

<div id="sendrequest" class="UICenterHorizontal UICenterVertical">
<h2 class="UISubtext UICenterHorizontal">Send friend request</h2>
<p class="UISubtext UICenterHorizontal">Send this person a friend request to start a conversation.</p>
<button class="UIButton UICenterHorizontal" id="sendrequest_button">SEND REQUEST</button>
</div>

<div id="acceptrequest" class="UICenterHorizontal UICenterVertical">
<h2 class="UISubtext UICenterHorizontal">New friend request</h2>
<p class="UISubtext UICenterHorizontal">Would you like to accept this friend request?</p>
<button class="UIButton UICenterHorizontal" id="acceptrequest_accept">ACCEPT</button>
<button class="UIButton UICenterHorizontal" id="acceptrequest_decline">DECLINE</button>
</div>

<div id="conversation">
<div id="conversation_messages"></div>
<div id="sendbar">
<input class="UITextfield" id="sendbox" type="text" placeholder="Send message..." />
<button class="UIButton" id="sendbutton">SEND</button>
</div>
</div>

</div>
<?php
print_footer('messages');
?>