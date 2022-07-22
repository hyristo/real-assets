<?php
include 'lib/api.php';

session_name(SESSION_NAME);
session_start();
session_unset();
session_destroy();
Utils::RedirectTo('login.php');
?>

