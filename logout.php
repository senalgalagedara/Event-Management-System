<?php
require_once 'auth.php';
session_destroy();
header("Location: login.php?msg=Logged+out");
exit();
?>