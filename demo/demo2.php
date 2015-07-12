<?php
(@include '../vendor/autoload.php') or die('Please use composer to install required packages.' . PHP_EOL);

$pathCDN	= "//cdn.int1a.net/";
$pathImages	= "images/";
$fileName	= "IMG_6661.JPG";
$fileName	= "IMG_2422.JPG";
$fileName	= "IMG_2466.JPG";
$fileName	= "IMG_2454.JPG";

$image		= new \CeusMedia\Image\Image( $pathImages.$fileName );	//  load image
$processor	= new \CeusMedia\Image\Processor( $image );				//  start processor on image
$processor->scaleDownToLimit( 1960, 800 );							//  scale down very large image

$offsetX	= (int) floor( ( $image->getWidth() - 980 ) / 2 );		//  calculate left crop offset
$offsetY	= (int) floor( ( $image->getHeight() - 450 ) / 2 );		//  calculate top crop offset
$processor->crop( $offsetX, $offsetY, 980, 450 );					//  crop middle image part

$copy	= clone $image;												//  close image for results
$processor	= new \CeusMedia\Image\Processor( $copy );				//  start processor on result image

//$processor->enhance( -10, 10, 0.8, 50 );							//  enhance image: preset "old film"
//$processor->enhance( 10, -10, 1.25, 10 );							//  enhance image: preset "person",
$processor->enhance( 10, -10, 1.25, 20 );							//  enhance image: preset "nature",

$body	= '
<div class="container">
	<h1 class="muted">CeusMedia Component Demo</h1>
	<h2>Image</h2>
	<p>Image Manipulation and Generation</p>
	<h3>Original <small class="muted">(scaled down and cropped)</small></h3>
	<img src="'.(string) $image.'"/><br/>
	<br/>
	<h3>Enhanced <small class="muted">(using CeusMedia\Image\Filter via CeusMedia\Image\Processor)</small></h3>
	<img src="'.(string) $copy.'"/><br/>
</div>
';

$page	= new \UI_HTML_PageFrame();
#$page->addJavaScript( $pathCDN.'js/bootstrap.min.js' );
$page->addStylesheet( $pathCDN.'css/bootstrap.min.css' );
//$page->addStylesheet( 'style.css' );
$page->addBody( $body );

print( $page->build() );
?>
