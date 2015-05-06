<?php

require_once('vendor/autoload.php');

$slice = new \PHPixie\Slice();
$filesystem = new \PHPixie\Filesystem(__DIR__);
$template = new \PHPixie\Template($slice, $filesystem, $slice->arrayData(array(
    'resolver' => array(
        'locator' => array(
            'directory' => '/templates/'
        )
    )
)));

echo $template->render('fairy', array(
    'name' => 'Stella'
));

$fairy = $template->get('fairy');
$fairy->name = 'Blum';

echo $fairy->render();