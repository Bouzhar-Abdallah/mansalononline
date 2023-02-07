<?php

spl_autoload_register(function($classname){
   require_once $filename = "app/models/". ucfirst($classname).".php";
});

require_once 'config.php';
require_once 'functions.php';
require_once 'Database.php';
require_once 'Model.php';
require_once 'Controller.php';
require_once 'App.php';
require_once 'app/utils/headers.php';
require_once 'app/utils/utilities.php';