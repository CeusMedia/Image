<?php
(@include '../vendor/autoload.php') or die('Please use composer to install required packages.' . PHP_EOL);

ini_set( 'display_errors', 'On' );

$image  = new CeusMedia\Image\Image();
$image->create( 200, 150, TRUE );
$image->setTransparentColor( $image->getColor( 255, 127, 0 ) );


$colorRed	= $image->getColor( 255, 0, 0 );
$colorBlue	= $image->getColor( 0, 0, 255 );
$colorGrey	= $image->getColor( 63, 63, 63 );

$drawer = new CeusMedia\Image\Drawer( $image );
$drawer->fillRectangle( 30, 20, 50, 35, $colorGrey );
$drawer->drawRectangle( 20, 15, 40, 30, $colorBlue );

//$image->display();

$base64 = CeusMedia\Image\Printer::toBase64( $image );

echo '<img src="data:image/png;base64,'.$base64.'"/>';
