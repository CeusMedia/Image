<?php
require_once 'cmClasses/trunk/autoload.php5';
require_once 'cmModules/trunk/autoload.php5';

$pathCDN	= "//cdn.int1a.net/";
$fileName	= "IMG_2422.JPG";
$fileName	= "IMG_6661.JPG";
$fileName	= "IMG_2454.JPG";

$image		= new CMM_IMG_Image( $fileName );
$processor	= new CMM_IMG_Processor( $image );
$processor->scaleDownToLimit( 250, 166 );
$original	= clone $image;

$flipped	= clone $original;
$processor	= new CMM_IMG_Processor( $flipped );
$processor->flip( CMM_IMG_Processor::FLIP_HORIZONTAL );

$rotated	= clone $original;
$processor	= new CMM_IMG_Processor( $rotated );
$processor->rotate( 180 );

$cropped	= clone $original;
$processor	= new CMM_IMG_Processor( $cropped );
$processor->crop( 0, 0, 125, 83 );
$processor->scale( 2 );

function applyFilter( $image, $filterName, $arguments = array() ){
	$clone	= clone( $image );
	$filter	= new CMM_IMG_Processor( $clone );
	$filter->filter( $filterName, $arguments );
	return $clone;
}

$body	= '
<div style="width: 980px">
	<h2><span class="muted">cmModules Demos</span> <abbr title="Image Manipulation and Generation">IMG</abbr></h2>
	<h3>Original <small class="muted">(already scaled down)</small></h3>
	<img src="'.(string) $original.'"/><br/>
	<br/>
	<h3>Processed <small class="muted">(using CMM_IMG_Processor)</small></h3>
	<div class="row-fluid">
		<div class="span4">
			<h4>Flipped</h4>
			<img src="'.(string) $flipped.'"/><br/>
			<code>flip( CMM_IMG_Processor::FLIP_HORIZONTAL )</code>
		</div>
		<div class="span4">
			<h4>Rotated <small class="muted">(by 180°)</small></h4>
			<img src="'.(string) $rotated.'"/><br/>
			<code>rotate( 180 )</code>
		</div>
		<div class="span4">
			<h4>Cropped <small class="muted">to half</small> & Scaled <small class="muted">by 2</small></h4>
			<img src="'.(string) $cropped.'"/><br/>
			<code>crop( 0, 0, 125, 83 ) & scale( 2 )</code>
		</div>
	</div>
	<br/>
	<h3>Filtered <small class="muted">(using CMM_IMG_Filter or CMM_IMG_Processor::filter)</small></h3>
	<h4>Contrast</h4>
	<div class="row-fluid">
		<div class="span4">
			<h5>Less <small class="muted">(-50%)</small></h5>
			<img src="'.(string) applyFilter( $original, "contrast", array( -50 ) ).'"/><br/>
			<code>contrast( -50 )</code>
		</div>
		<div class="span4">
			<h5>More <small class="muted">(+50%)</small></h5>
			<img src="'.(string) applyFilter( $original, "contrast", array( 50 ) ).'"/><br/>
			<code>contrast( 50 )</code>
		</div>
	</div>
	<br/>
	<h4>Brightness</h4>
	<div class="row-fluid">
		<div class="span4">
			<h5>Brightened <small class="muted">(+50%)</small></h5>
			<img src="'.(string) applyFilter( $original, "brightness", array( 63 ) ).'"/><br/>
			<code>brightness( 63 )</code>
		</div>
		<div class="span4">
			<h5>Darkened <small class="muted">(-50%)</small></h5>
			<img src="'.(string) applyFilter( $original, "brightness", array( -63 ) ).'"/><br/>
			<code>brightness( -63 )</code>
		</div>
	</div>
	<br/>
	<h4>Gamma</h4>
	<div class="row-fluid">
		<div class="span4">
			<h5>More <small class="muted">(+50%)</small></h5>
			<img src="'.(string) applyFilter( $original, "gamma", array( 1.5 ) ).'"/><br/>
			<code>gamma( 1.5 )</code>
		</div>
		<div class="span4">
			<h5>Less <small class="muted">(-50%)</small></h5>
			<img src="'.(string) applyFilter( $original, "gamma", array( 0.5 ) ).'"/><br/>
			<code>gamma( 0.5 )</code>
		</div>
	</div>
	<br/>
	<h4>Bluring / Smoothing</h4>
	<div class="row-fluid">
		<div class="span4">
			<h5>Gaussian blur</h5>
			<img src="'.(string) applyFilter( $original, "blurGaussian" ).'"/><br/>
			<code>blurGaussian()</code>
		</div>
		<div class="span4">
			<h5>Selective blur</h5>
			<img src="'.(string) applyFilter( $original, "blurSelective" ).'"/><br/>
			<code>blurSelective()</code>
		</div>
		<div class="span4">
			<h5>Smooth</h5>
			<img src="'.(string) applyFilter( $original, "smooth", array( 1 ) ).'"/><br/>
			<code>smooth( 1 )</code>
		</div>
	</div>
	<br/>
	<h4>Effects</h4>
	<div class="row-fluid">
		<div class="span4">
			<h5>Invert</h5>
			<img src="'.(string) applyFilter( $original, "negate" ).'"/><br/>
			<code>negate()</code>
		</div>
		<div class="span4">
			<h5>Remove mean</h5>
			<img src="'.(string) applyFilter( $original, "removeMean" ).'"/><br/>
			<code>removeMean()</code>
		</div>
		<div class="span4">
			<h5>Edges</h5>
			<img src="'.(string) applyFilter( $original, "detectEdges" ).'"/><br/>
			<code>detectEdges()</code>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span4">
			<h5>Emboss</h5>
			<img src="'.(string) applyFilter( $original, "emboss" ).'"/><br/>
			<code>emboss()</code>
		</div>
		<div class="span4">
			<h5>Grayscale</h5>
			<img src="'.(string) applyFilter( $original, "grayscale" ).'"/><br/>
			<code>grayscale()</code>
		</div>
	</div>
	<br/>
	<h4>Effect: Colorize</h4>
	<div class="row-fluid">
		<div class="span4">
			<h5>Redish</h5>
			<img src="'.(string) applyFilter( $original, "colorize", array( 63, -31, -31 ) ).'"/><br/>
			<code>colorize( -63, 0, 127 )</code>
		</div>
		<div class="span4">
			<h5>Greenish</h5>
			<img src="'.(string) applyFilter( $original, "colorize", array( -63, 91, -31 ) ).'"/><br/>
			<code>colorize( -63, 111, -31 )</code>
		</div>
		<div class="span4">
			<h5>Sepia</h5>
			<img src="'.(string) applyFilter( $original, "sepia" ).'"/><br/>
			<code>sepia()</code>
		</div>
	</div>
	<br/>
	<h4>Effect: Pixelate <small class="muted">version 2</small></h4>
	<div class="row-fluid">
		<div class="span4">
			<h5>Even</h5>
			<img src="'.(string) applyFilter( $original, "pixelate2", array( 10, 10 ) ).'"/><br/>
			<code>pixelate2( 10, 10 )</code>
		</div>
		<div class="span4">
			<h5>Horizonal</h5>
			<img src="'.(string) applyFilter( $original, "pixelate2", array( 20, 1 ) ).'"/><br/>
			<code>pixelate2( 30, 1 )</code>
		</div>
		<div class="span4">
			<h5>Vertical</h5>
			<img src="'.(string) applyFilter( $original, "pixelate2", array( 1, 20 ) ).'"/><br/>
			<code>pixelate2( 1, 20 )</code>
		</div>
	</div>
</div>
';

$page	= new UI_HTML_PageFrame();
#$page->addJavaScript( $pathCDN.'js/bootstrap.min.js' );
$page->addStylesheet( $pathCDN.'css/bootstrap.min.css' );
$page->addStylesheet( 'style.css' );
$page->addBody( $body );

print( $page->build() );
?>
