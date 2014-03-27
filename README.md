# KopiKode Container

A PHP array container for simplification usability

## Requirement

You need **PHP >= 5.3.0**.

## Installation

Add requirements to your composer json files


     "require": {
        "kopikode/container": "2.*"
    }

## Manual

PHP Native array access
    
    // Set
    $arr = array();
    $arr['user']['administrator'] = 'administrator';

    // Get
    $admin = $arr['user']['administrator];

Container Access
    // Initiate 
    $container = new \KopiKode\Container;

    // Set
    $container['user.administrator] = 'administrator';
    
    // Get
    $admin = $container['user.administrator'];

Nested (get upper level )
    
    $users = $container['user'];