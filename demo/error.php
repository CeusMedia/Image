<?php
(@include '../vendor/autoload.php') or die('Please use composer to install required packages.' . PHP_EOL);

//ini_set( 'display_errors', 'On' );

$error  = new CeusMedia\Image\Error( 'This is an error image.' );
