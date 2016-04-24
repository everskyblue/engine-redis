<?php

include 'view/Autoload.php';

$base = __DIR__;

$view = new View\View($base . '/resources/', [

	'compile' => $base . '/storage/',
	'cache' => true

]);

$std = new stdClass();
$std->age = 35;
$std->country = 'spain';

$view->render('app')->assign(['name' => 'pedro', 'title' => 'home', 'obj' => $std])->create();
