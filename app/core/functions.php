<?php

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

function redirect($path){
    header("location: ".ROOT.$path);
    die;
}


