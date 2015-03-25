<?php

require_once('vendor/autoload.php');

$slice = new \PHPixie\Slice();
$template = new \PHPixie\Template($slice, $slice->arrayData(array(
    'resolver' => array(
        'locator' => array(
            'directory' => __DIR__.'/templates/'
        )
    )
)));

echo $template->render('fairy', array(
    'name' => 'Stella'
));

$fairy = $template->get('fairy');
$fairy->name 