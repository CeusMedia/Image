<?php
(@include '../vendor/autoload.php') or die('Please use composer to install required packages.' . PHP_EOL);

$pathCDN	= "//cdn.int1a.net/";
$pathImages	= "images/";
$fileName	= "IMG_2422.JPG";
$fileName	= "IMG_6661.JPG";
$fileName	= "IMG_2454.JPG";
$fileName	= "IMG_2466.JPG";

$image		= new \CeusMedia\Image\Image( $pathImages.$fileName );		//  load image
\CeusMedia\Image\Processor::apply( $image, 'crop', array(
	(int) floor( ( $image->getWidth() - 980 ) / 2 ),
	(int) floor( ( $image->getHeight() - 250 ) / 2 ),
	980, 250 ) );

$copy		= clone $image;												//  close image for results
\CeusMedia\Image\Processor::apply( $copy, 'enhance' );
\CeusMedia\Image\Processor::applyFilter( $copy, 'grayscale' );
\CeusMedia\Image\Processor::applyFilter( $copy, 'gamma', array( 2.5 ) );
\CeusMedia\Image\Processor::applyFilter( $copy, 'brightness', array( -20 ) );
//\CeusMedia\Image\Processor::applyFilter( $copy, 'contrast', array( 20 ) );

$body	= '
<div class="container">
	<h1 class="muted">CeusMedia Component Demo</h1>
	<h2>Image</h2>
	<p>Image Manipulation and Generation</p>
	<p>
		This demonstration showcases use of static methods of <code>CeusMedia\Image\Processor</code> and  <code>CeusMedia\Image\Filter</code>.
	</p>
	<h3>Original <small class="muted">(scaled down and cropped)</small></h3>
	<img src="'.(string) $image.'"/><br/>
	<br/>
	<h3>Enhanced <small class="muted">(using CeusMedia\Image\Filter via CeusMedia\Image\Processor statically)</small></h3>
	<img src="'.(string) $copy.'"/><br/>
</div>
';

$page	= new \UI_HTML_PageFrame();
#$page->addJavaScript( $pathCDN.'js/bootstrap.min.js' );
$page->addStylesheet( $pathCDN.'css/bootstrap.min.css' );
//$page->addStylesheet( 'style.css' );
$page->addBody( $body );

print( $page->build( array() ) );
?>
