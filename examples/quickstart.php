<?php

require_once('vendor/autoload.php');

//Required libraries
$slice = new \PHPixie\Slice();
$filesystem = new \PHPixie\Filesystem();

//Configuration
$locatorConfig = $slice->arrayData(array(
    'directory' => '/templates/'
));
$templateConfig = $slice->arrayData(array(
    //Let's just use defaults
));

//Build dependencies
$root = $filesystem->root(__DIR__);
$locator = $filesystem->buildlocator($locatorConfig, $root);

//And the Template library itself
$template = new \PHPixie\Template($slice, $locator, $templateConfig);

echo $template->render('fairy', array(
    'name' => 'Stella'
));

$fairy = $template->get('fairy');
$fairy->name = 'Blum';

echo $fairy->render();