<?php

function autoload($className)
{
    $baseDir = realpath(__DIR__.'/../src/');

    $className = ltrim($className, '\\');
    $fileName  = $baseDir;
    $namespace = '';
    if ($lastNsPos = strripos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  .= DIRECTORY_SEPARATOR .str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    
    echo $fileName;
    
    if (file_exists($fileName)) {
        require $fileName;
    }
}

spl_autoload_register(__NAMESPACE__ . "autoload");
