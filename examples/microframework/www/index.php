<?php

/**
 * @category  Sonno
 * @author    Dave Hauenstein <davehauenstein@gmail.com>
 * @author    Tharsan Bhuvanendran <me@tharsan.com>
 * @author    360i <sonno@360i.com>
 * @copyright Copyright (c) 2011 360i LLC (http://360i.com)
 * @license   http://sonno.360i.com/LICENSE.txt New BSD License
 */

define('APPLICATION_PATH', realpath(__DIR__ . '/../'));

// Require our autoloader. It simplest to use Doctrine's Autoloader since
// Doctrine-Common is already installed for Annotation reading and is on the 
// include path, however, it's not required to use Doctrine's.
require_once __DIR__ . '/../app/autoload.php';

// The use statement here declares all of the classes needed to get Sonno
// up and running.
use Sonno\Configuration\Driver\AnnotationDriver,
    Sonno\Annotation\Reader\DoctrineReader,
    Sonno\Application\Application,
    Sonno\Http\Request\Request,
    Doctrine\Common\Annotations\AnnotationReader,
    Doctrine\Common\Annotations\AnnotationRegistry;

// Configure Doctrine's annotation reader. For more information see the
// following link:
// http://doctrine-project.org/docs/common/2.1/en/reference/annotations.html
$doctrineReader = new AnnotationReader();
AnnotationRegistry::registerAutoloadNamespace(
    'Sonno\Annotation',
    realpath(__DIR__ . '/../../../src')
);

// Sonno ships with one Annotation reader adapter, and that supports Doctrine's
// Annotation reader. Sonno's adapter-based architecture allows for developers
// to write Annotation readers using any library. The
// Sonno\Annotation\Reader\ReaderInterface is provided so different readers can
// use different engines.
$annotationReader = new DoctrineReader($doctrineReader);
// An array of Resource classes must be provided so the AnnotationDriver knows
// where to find annotated resource classes.
$resources = array(
    'Sonno\Example\Resource\RootResource',
);

// Sonno\Configuration\Configuration objects are retrieved through Configuration
// Drivers. A driver must implement the
// Sonno\Configuration\Driver\DriverInterface interface. Sonno ships with a
// driver that parses annotations, but future versions will ship with config
// drivers for XML, YAML, and anything else that may make sense.
$driver = new AnnotationDriver($resources, $annotationReader);
$config = $driver->parseConfig();

// It's also possible to set other config options, such as the base path.
// $config->setBasePath('/api/rest/v1');

// The application class requires a Sonno\Configuration\Configuration object
// to run. This object can be cached so annotations don't have to be read and
// parsed on every request. It's also not necessary that this config object
// is created using annotations. 
$application = new Application($config);
$application->run(Request::getInstanceOfCurrentRequest());
