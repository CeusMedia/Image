<?php
require_once 'cmClasses/trunk/autoload.php5';
require_once 'cmModules/trunk/autoload.php5';

$pathCDN	= "//cdn.int1a.net/";
$fileName	= "IMG_2422.JPG";
$fileName	= "IMG_6661.JPG";
$fileName	= "IMG_2454.JPG";
$fileName	= "IMG_2466.JPG";

$image		= new CMM_IMG_Image( $fileName );						//  load image
CMM_IMG_Processor::apply( $image, 'crop', array(
	(int) floor( ( $image->getWidth() - 980 ) / 2 ),
	(int) floor( ( $image->getHeight() - 250 ) / 2 ),
	980, 250 ) );

$copy		= clone $image;												//  close image for results
CMM_IMG_Processor::apply( $copy, 'enhance' );
CMM_IMG_Processor::applyFilter( $copy, 'grayscale' );
CMM_IMG_Processor::applyFilter( $copy, 'gamma', array( 2.5 ) );
CMM_IMG_Processor::applyFilter( $copy, 'brightness', array( -20 ) );
#CMM_IMG_Processor::applyFilter( $copy, 'contrast', array( 20 ) );

$body	= '
<div style="width: 980px">
	<h2><span class="muted">cmModules Demos</span> <abbr title="Image Manipulation and Generation">IMG</abbr></h2>
	<p>
		This demonstration showcases use of static methods of <code>CMM_IMG_Processor</code> and  <code>CMM_IMG_Filter</code>.
	</p>
	<h3>Original <small class="muted">(scaled down and cropped)</small></h3>
	<img src="'.(string) $image.'"/><br/>
	<br/>
	<h3>Enhanced <small class="muted">(using CMM_IMG_Filter via CMM_IMG_Processor statically)</small></h3>
	<img src="'.(string) $copy.'"/><br/>
</div>
';

$page	= new UI_HTML_PageFrame();
#$page->addJavaScript( $pathCDN.'js/bootstrap.min.js' );
$page->addStylesheet( $pathCDN.'css/bootstrap.min.css' );
$page->addStylesheet( 'style.css' );
$page->addBody( $body );

print( $page->build( array() ) );
?>
