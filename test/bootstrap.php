<?php

$root = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR;
require_once($root.'lib/xframe/autoloader/Autoloader.php');

$autoloader = new xframe\autoloader\Autoloader($root);
$autoloader->register();