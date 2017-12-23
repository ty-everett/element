<?php
require_once('/srv/http/element.tk/security/check.php');
include("header.php");
printHeader("Element Technical Blog");
?>
<div class="content">
<h1>Element Technical Blog</h1>
<p class="date">Published 6/28/2017</p>
<p>This page will update as I work on things related to the project.
As of right now, it's still in it's infancy but you can expect a lot to happen
within the next few months. I expect to have at <i>least</i> a working
prototype by January 2018.</p>
<h1>Sketching the UI</h1>
<p class="date">Published 7/0/2017</p>
<p>The UI for the app needs to be standardized across all platforms. This will
deliver a consistant user experience and make it easier for people to use
the app across devices. To solve this problem, I've created a basic sketch
for the launchtime UI available <a href="/design/mobile.pdf">here</a></p>
<h1>Tackling Android</h1>
<p class="date">Published 8/8/2017</p>
<p>I've started work on the Android app and learned a lot about the platform
in the process. I've implemented a WebView to display the application, and
created some basic graphics and a UI. Still on track for a January 2018 launch!</p>
</div>
<?php
include("footer.php");
printFooter();
?>