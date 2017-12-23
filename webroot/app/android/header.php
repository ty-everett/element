<?php

function require_login(){
	if($_SESSION['isLoggedIn'] != 1){
		header('location: /app/');
		echo('Please log in first.');
		die();
	}
}
function print_footer($tab){
?>
<div class="bottomtabs">
<div class="tab<?php if($tab == 'posts'){echo(' currenttab');} ?>"><a href="../posts/"><img src="../img/posts_icon.png" /></a></div>
<div class="tab<?php if($tab == 'anonymous'){echo(' currenttab');} ?>"><a href="../anonymous/"><img src="../img/anon_icon.png" /></a></div>
<div class="tab<?php if($tab == 'privacy'){echo(' currenttab');} ?>"><a href="../privacy/"><img src="../img/privacy_icon.png" /></a></div>
<div class="tab<?php if($tab == 'productivity'){echo(' currenttab');} ?>"><a href="../productivity/"><img src="../img/productivity_icon.png" /></a></div>
<div class="tab<?php if($tab == 'messages'){echo(' currenttab');} ?>"><a href="../messaging/"><img src="../img/messages_icon.png" /></a></div>
<div class="tab<?php if($tab == 'profile'){echo(' currenttab');} ?>"><a href="../profile/"><img src="../img/profile_icon.png" /></a></div>
</div>
<div id="error_div">
</div>
</body>
</html>
<?php }
?>