<?php
require_once 'cmClasses/trunk/autoload.php5';
require_once 'cmModules/trunk/autoload.php5';

$pathCDN	= "//cdn.int1a.net/";
$fileName	= "IMG_6661.JPG";
$fileName	= "IMG_2422.JPG";
$fileName	= "IMG_2466.JPG";
$fileName	= "IMG_2454.JPG";

$image		= new CMM_IMG_Image( $fileName );						//  load image
$processor	= new CMM_IMG_Processor( $image );						//  start processor on image
$processor->scaleDownToLimit( 1960, 800 );							//  scale down very large image

$offsetX	= (int) floor( ( $image->getWidth() - 980 ) / 2 );		//  calculate left crop offset
$offsetY	= (int) floor( ( $image->getHeight() - 450 ) / 2 );		//  calculate top crop offset
$processor->crop( $offsetX, $offsetY, 980, 450 );					//  crop middle image part

$copy	= clone $image;												//  close image for results
$processor	= new CMM_IMG_Processor( $copy );						//  start processor on result image

//$processor->enhance( -10, 10, 0.8, 50 );							//  enhance image: preset "old film"
//$processor->enhance( 10, -10, 1.25, 10 );							//  enhance image: preset "person",
$processor->enhance( 10, -10, 1.25, 20 );							//  enhance image: preset "nature",

$body	= '
<div style="width: 980px">
	<h2><span class="muted">cmModules Demos</span> <abbr title="Image Manipulation and Generation">IMG</abbr></h2>
	<h3>Original <small class="muted">(scaled down and cropped)</small></h3>
	<img src="'.(string) $image.'"/><br/>
	<br/>
	<h3>Enhanced <small class="muted">(using CMM_IMG_Filter via CMM_IMG_Processor)</small></h3>
	<img src="'.(string) $copy.'"/><br/>
</div>
';

$page	= new UI_HTML_PageFrame();
#$page->addJavaScript( $pathCDN.'js/bootstrap.min.js' );
$page->addStylesheet( $pathCDN.'css/bootstrap.min.css' );
$page->addStylesheet( 'style.css' );
$page->addBody( $body );

print( $page->build() );
?>
