<?php
require 'config.php';
session_destroy();
header("Location: /car-rental/login.php");
exit;
