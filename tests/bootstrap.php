<?php
//var_dump(dirname(__DIR__) . '/vendor/autoload.php');

if (!require dirname(__DIR__) . '/vendor/autoload.php') {
    die('You must set up the project dependencies, run the following commands:
        wget http://getcomposer.org/composer.phar
        php composer.phar install');
}
