<?php

session_start();

require_once "app/core/init.php";
/* echo '<br>';
echo '<br>'; */
//show($_SESSION);
DEBUG ? ini_set('display_errors', 1) : ini_set('display_errors', 0);

$app = new App;
$app->loadController();