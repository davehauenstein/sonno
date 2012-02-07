<?php

/**
 * @category  Sonno
 * @author    Dave Hauenstein <davehauenstein@gmail.com>
 * @author    Tharsan Bhuvanendran <me@tharsan.com>
 * @author    360i <sonno@360i.com>
 * @copyright Copyright (c) 2011 360i LLC (http://360i.com)
 * @license   http://sonno.360i.com/LICENSE.txt New BSD License
 */

use Doctrine\Common\ClassLoader;
require 'Doctrine/Common/ClassLoader.php';

$classLoader = new ClassLoader(
    'Sonno\Example',
    __DIR__
);
$classLoader->register();
unset($classLoader);

$sonnoLoader = new ClassLoader(
    'Sonno',
    realpath(__DIR__ . '/../../../src')
);
$sonnoLoader->register();
unset($sonnoLoader);

$commonLoader = new ClassLoader(
    'Doctrine\Common'
);
$commonLoader->register();
unset($commonLoader);