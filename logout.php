<?php
require 'includes/config.php';
require 'includes/functions.php';

session_destroy();
redirect('index.php');
?>
