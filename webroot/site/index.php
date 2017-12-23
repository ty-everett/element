<?php
require_once('/srv/http/element.tk/security/check.php');
include("header.php");
printHeader("Element");
?>
<div class="content">
<h1>Welcome to Element!</h1>
<p>Element is going to be the world's solution to the problem of privacy and
distraction on the internet. Our app is still being built, but in the meantime you
can check out our technical blog, share our <a href="/video.php">launch video</a>, and sign up for
updates!</p>
<h2>Sign up for updates!</h2>
<p>We'll let you know as things are developed, when new features are added,
and keep you updated in general when things happen.</p>
<center>
<form method="post" action="signup.php">
<input type="email" name="email" placeholder="Email" /><br/>
<input type="submit" value="Sign Up" />
</form>
</center>
<h2>The Problem With Social Media</h2>
<p>One of the main problems with social media is that it distracts and
diverts our attention from what we care about in the real world. In so
doing, it's also monetizing our content and experiences, collecting and
selling our personal information to advertisers, and in some countries
giving governments access to the data of millions of people.</p>
<h2>The Elemental Solution</h2>
<p>Element Communications is striving to solve the problems outlined above.
We're encrypting everything we create so as to block it from prying eyes,
our code is open-source and public, and we don't share anything with any
third parties. Although we realize this won't completely solve these
problems, we aim to provide a place where people who care about such things
as privacy and their personal data can go to communicate and enjoy unmnnetized
content in moderation.</p>
</div>
<?php
include("footer.php");
printFooter();
?>
