# KopiKode Container


[![Build Status](https://travis-ci.org/somy/Container.svg?branch=master)](https://travis-ci.org/somy/Container)

A PHP array container for simplification usability

## Requirement

You need **PHP >= 5.3.0**.

## Installation

Add requirements to your composer json files


     "require": {
        "kopikode/container": "*"
    }

## Manual

PHP Native array access
    
    // Set
    $arr = array();
    $arr['user']['administrator'] = 'Somy A';

    // Get
    $admin = $arr['user']['administrator];

Container Access

    // Initiate 
    $container = new \KopiKode\Container;

    // Set
    $container['user.administrator] = 'Somy A';
    
    // Get
    $admin = $container['user.administrator'];

Nested Access (get upper level )
    
    $users = $container['user'];

will reproduce 
    
    array (
        'administrator' => 'Somy A'
    )