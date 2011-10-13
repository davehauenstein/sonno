<?php

require_once "Doctrine/Common/ClassLoader.php";

use Doctrine\Common\ClassLoader;

$loader = new ClassLoader('Sonno\Test', __DIR__ . '/Test');
$loader->register();
$loader = new ClassLoader('Sonno', __DIR__ . '/../src');
$loader->register();
$loader = new ClassLoader('Doctrine');
$loader->register();
unset($loader);

