<?php
    // require_once __DIR__."/libraries/Controller.php";
    // require_once __DIR__."/libraries/Core.php";
    require_once __DIR__."/utils/headers.php";
    require_once __DIR__."/utils/utilities.php";
    // echo "bootstrap";
    spl_autoload_register(function($classname){
        require_once ("libraries/".$classname.".php");
    });



function show($stuff)
{
    echo "<pre>";
    print_r($stuff);
    echo "</pre>";
    //die();
}
function showd($stuff)
{
    echo "<pre>";
    print_r($stuff);
    echo "</pre>";
    die();
}