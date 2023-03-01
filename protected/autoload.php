<?php

require __DIR__ . "/../vendor/autoload.php";

spl_autoload_register(function($class){
    if (stripos($class, "App") !== false){
        $path = __DIR__ . "/" . str_replace('\\', '/', substr($class, 4)) . ".php";
        require $path;
    }
});