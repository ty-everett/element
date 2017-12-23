<?php

include('header.php');

requireLogin();

echo($_SESSION['username']);

?>