<?php
/**
 *	Processor for resizing, scaling and rotating an image.
 *
 *	Copyright (c) 2010-2015 Christian Würker (ceusmedia.com)
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *	@category		Library
 *	@package		CeusMedia_Image
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2010-2015 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Image
 */
namespace CeusMedia\Image;
/**
 *	Processor for resizing, scaling and rotating an image.
 *	@category		Library
 *	@package		CeusMedia_Image
 *	@uses			\CeusMedia\Image\Image
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2010-2015 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Image
 */
class Processor{

	/**	@var		\CeusMedia\Image\Image		$image			Image resource object */
	protected $image;

	/**	@param		integer			$maxMegaPixel	Maxiumum megapixels */
	public $maxMegaPixels			= 1;

	const FLIP_HORIZONTAL			= 0;
	const FLIP_VERTICAL				= 1;

	/**
	 *	Constructor.
	 *	Sets initial image resource object.
	 *	@access		public
	 *	@param		\CeusMedia\Image\Image	$image			Image resource object
	 *	@param		float			$maxMegaPixel	Maxiumum megapixels, default: 50, set 0 to disable
	 *	@return		void
	 */
	public function __construct( \CeusMedia\Image\Image $image, $maxMegaPixels = 50 ){
		$this->image			= $image;
		if( !is_null( $maxMegaPixels ) )
			$this->maxMegaPixels	= $maxMegaPixels;
	}

	static public function apply( \CeusMedia\Image\Image $image, $processName, $arguments = array() ){
		$processor		= new self( $image );
		if( !method_exists( $processor, $processName ) )
			throw new \OutOfRangeException( 'Invalid process "'.$processName.'"' );
		\Alg_Object_MethodFactory::callObjectMethod( $processor, $processName, $arguments );
	}

	static public function applyFilter( \CeusMedia\Image\Image $image, $filterName, $arguments = array() ){
		\CeusMedia\Image\Filter::apply( $image, $filterName, $arguments );
	}

	/**
	 *	Crop image.
	 *	@access		public
	 *	@param		integer		$startX			Left margin
	 *	@param		integer		$startY			Top margin
	 *	@param		integer		$width			New width
	 *	@param		integer		$height			New height
	 *	@return		object		Processor object for chaining
	 *	@throws		InvalidArgumentException if left margin is not an integer value
	 *	@throws		InvalidArgumentException if top margin is not an integer value
	 *	@throws		InvalidArgumentException if width is not an integer value
	 *	@throws		InvalidArgumentException if height is not an integer value
	 *	@throws		OutOfRangeException if width is lower than 1
	 *	@throws		OutOfRangeException if height is lower than 1
	 *	@throws		OutOfRangeException if resulting image has more mega pixels than allowed
	 */
	public function crop( $startX, $startY, $width, $height ){
		if( !is_int( $startX ) )
			throw new \InvalidArgumentException( 'X start value must be integer' );
		if( !is_int( $startY ) )
			throw new \InvalidArgumentException( 'Y start value must be integer' );
		if( !is_int( $width ) )
			throw new \InvalidArgumentException( 'Width must be integer' );
		if( !is_int( $height ) )
			throw new \InvalidArgumentException( 'Height must be integer' );
		if( $width < 1 )
			throw new \OutOfRangeException( 'Width must be atleast 1' );
		if( $height < 1 )
			throw new \OutOfRangeException( 'Height must be atleast 1' );
		$image	= new \CeusMedia\Image\Image;
		$image->create( $width, $height );
		$image->setType( $this->image->getType() );
		imagecopy( $image->getResource(), $this->image->getResource(), 0, 0, $startX, $startY, $width, $height );

		$this->image->setResource( $image->getResource() );											//  replace held image resource object by result
		return $this;
	}

	/**
	 *	Improves image by applying several filters.
	 *	By default it increase gamma and contract, darkens a bit and adds a bit sharpness.
	 *	Attention: Results may vary. Please change values for your taste or needs.
	 *	@access		public
	 *	@param		integer		$contrast		Adjust contrast: -100 min, 0 no change, +100 max
	 *	@param		integer		$brightness		Adjust brightness: -100 min, 0 no change, +100 max
	 *	@param		float		$gamma			Adjust gamma: 0<x<1 less, 1 no change, 1<x more
	 *	@param		integer		$sharpen		Adjust sharpness: 0 no change, 100 max
	 *	@return		object		Processor object for chaining
	 */
	public function enhance( $contrast = 10, $brightness = -10, $gamma = 1.23, $sharpen = 10 ){
		$contrast		= (int) min( 100, max( -100, $contrast ) );									//  sanitize contrast
		$brightness		= (int) min( 100, max( -100, $brightness ) );								//  sanitize brightness
		$gamma			= max( 0, $gamma );															//  sanitize gamma
		$sharpen		= min( 100, max( 0, $sharpen ) );											//  sanitize sharpness
		$this->filter( 'gamma', array( $gamma ) );
		$this->filter( 'contrast', array( $contrast ) );
		$this->filter( 'brightness', array( $brightness ) );
		if( $sharpen ){																				//  add sharpness by...
			$layerSharp		= clone $this->image;													//  ... clone image for sharpness layer
			$filterLayer	= new \CeusMedia\Image\Filter( $layerSharp );									//  ... start filter on layer image
			$filterLayer->removeMean();																//  apply filter to remove mean color values
			imagecopymerge(																			//  overlay sharper image
				$this->image->getResource(),														//  main image
				$layerSharp->getResource(),															//  sharpness layer image
				0, 0,																				//  start coordinates in main image
				0, 0,																				//  start coordinates in sharpness layer image
				$this->image->getWidth(),															//  width of resulting image
				$this->image->getHeight(),															//  height of resulting image
				$sharpen																			//  opacity
			);
		}
		return $this;
	}

	/**
	 *	Applies a filter to image.
	 *	@access		public
	 *	@param		string		$filterName		Name of filter to apply
	 *	@param		array		$arguments		Map of filter arguments
	 *	@return		object		Processor object for chaining
	 *	@throws		OutOfRangeException			if filter name is unknown
	 */
	public function filter( $filterName, $arguments = array() ){
		$filter		= new \CeusMedia\Image\Filter( $this->image );
		if( !method_exists( $filter, $filterName ) )
			throw new \OutOfRangeException( 'Invalid filter "'.$filterName.'"' );
		\Alg_Object_MethodFactory::callObjectMethod( $filter, $filterName, $arguments );
		return $this;
	}

	/**
	 *	Flips image horizontally or vertically.
	 *	@access		public
	 *	@param		integer		$mode		0: horizontally, 1: vertically
	 *	@return		object		Processor object for chaining
	 */
	public function flip( $mode = 0 ){
		$image	= new \CeusMedia\Image\Image;
		$width	= $this->image->getWidth();
		$height	= $this->image->getHeight();
		$image->create( $width, $height );
		if( $mode == 1 ){
			imagecopyresampled(
				$image->getResource(),	$this->image->getResource(),
				0, 0,
				0, ( $height - 1),
				$width, $height,
				$width, 0 - $height
			);
		}
		else{
			imagecopyresampled(
				$image->getResource(),	$this->image->getResource(),
				0, 0,
				( $width - 1), 0,
				$width, $height,
				0 - $width, $height
			);
		}
		$this->image->setResource( $image->getResource() );											//  replace held image resource object by result
		return $this;
	}

	/**
	 *	Resizes image.
	 *	@access		public
	 *	@param		integer		$width			New width
	 *	@param		integer		$height			New height
	 *	@param		boolean		$interpolate	Flag: use interpolation
	 *	@return		object		Processor object for chaining
	 *	@throws		InvalidArgumentException if width is not an integer value
	 *	@throws		InvalidArgumentException if height is not an integer value
	 *	@throws		OutOfRangeException if width is lower than 1
	 *	@throws		OutOfRangeException if height is lower than 1
	 *	@throws		OutOfRangeException if resulting image has more mega pixels than allowed
	 */
	public function resize( $width, $height, $interpolate = TRUE ){
		if( !is_int( $width ) )
			throw new \InvalidArgumentException( 'Width must be integer' );
		if( !is_int( $height ) )
			throw new \InvalidArgumentException( 'Height must be integer' );
		if( $width < 1 )
			throw new \OutOfRangeException( 'Width must be atleast 1' );
		if( $height < 1 )
			throw new \OutOfRangeException( 'Height must be atleast 1' );
		if( $this->image->getWidth() == $width && $this->image->getHeight() == $height )
			return $this;
		if( $this->maxMegaPixels && $width * $height > $this->maxMegaPixels * 1024 * 1024 )
			throw new \OutOfRangeException( 'Larger than '.$this->maxMegaPixels.'MP ('.$width.'x'.$height.')' );

		$image	= new \CeusMedia\Image\Image;
		$image->create( $width, $height );
		$image->setType( $this->image->getType() );

		$parameters	= array_merge(																	//  combine parameters from:
			array( $image->getResource(), $this->image->getResource() ),							//  target and source resources
			array( 0, 0, 0, 0 ),																	//  target and source start coordinates
			array( $width, $height ),																//  target width and height
			array( $this->image->getWidth(), $this->image->getHeight() )							//  source width and height
		);

		$function = $interpolate ? 'imagecopyresampled' : 'imagecopyresized';						//  function to use depending on interpolation
		$reflection	= new \ReflectionFunction( $function );											//  reflect function
		$reflection->invokeArgs( $parameters );														//  call function with parameters

		$this->image->setResource( $image->getResource() );											//  replace held image resource object by result
		return $this;
	}

	/**
	 *	Rotates image clockwise.
	 *	Resulting image may have different dimensions.
	 *	@access		public
	 *	@param		integer		$angle			Angle to rotate (0-360)
	 *	@param		integer		$bgColor		Background color
	 *	@param		integer		$transparency	Flag: use transparency
	 *	@return		object		Processor object for chaining
	 */
	public function rotate( $angle, $bgColor = 0, $ignoreTransparent = 0 ){
		$bgColor	= $this->image->colorTransparent;
		$this->image->setResource( imagerotate( $this->image->getResource(), -$angle, $bgColor ) );
		return $this;
	}

	/**
	 *	Scales image by factors.
	 *	If no factor for height is given, it will be the same as for width.
	 *	@access		public
	 *	@param		float		$factorWidth	Factor for width
	 *	@param		float		$factorHeight	Factor for height
	 *	@param		boolean		$interpolate	Flag: use interpolation
	 *	@return		object		Processor object for chaining
	 *	@throws		OutOfRangeException if resulting image has more mega pixels than allowed
	 */
	public function scale( $factorWidth, $factorHeight = NULL, $interpolate = TRUE ){
		if( is_null( $factorHeight ) )
			$factorHeight	= $factorWidth;
		if( $factorWidth == 1 && $factorHeight == 1 )
			return $this;
		$width	= (int) round( $this->image->getWidth() * $factorWidth );
		$height	= (int) round( $this->image->getHeight() * $factorHeight );
		$pixels	= $width * $height;
		if( $this->maxMegaPixels && $pixels > ( $this->maxMegaPixels * 1024 * 1024 ) )
			throw new \OutOfRangeException( 'Larger than '.$this->maxMegaPixels.'MP ('.$width.'x'.$height.')' );
		return $this->resize( $width, $height, $interpolate, $this->maxMegaPixels );
	}

	/**
	 *	Scales image down to a maximum size if larger than limit.
	 *	@access		public
	 *	@param		integer		$width			Maximum width
	 *	@param		integer		$height			Maximum height
	 *	@param		boolean		$interpolate	Flag: use interpolation
	 *	@param		integer		$maxMegaPixel	Maxiumum megapixels
	 *	@return		object		Processor object for chaining
	 */
	public function scaleDownToLimit( $width, $height, $interpolate = TRUE, $maxMegaPixel = 50 ){
		if( !is_int( $width ) )
			throw new \InvalidArgumentException( 'Width must be integer' );
		if( !is_int( $height ) )
			throw new \InvalidArgumentException( 'Height must be integer' );
		$sourceWidth	= $this->image->getWidth();
		$sourceHeight	= $this->image->getHeight();
		if( $sourceWidth <= $width && $sourceHeight <= $height )
			return $this;
		$scale = 1;
		if( $sourceWidth > $width )
			$scale	*= $width / $sourceWidth;
		if( $sourceHeight * $scale > $height )
			$scale	*= $height / ( $sourceHeight * $scale );
		$width	= (int) round( $sourceWidth * $scale );
		$height	= (int) round( $sourceHeight * $scale );
		return $this->resize( $width, $height, $interpolate, $maxMegaPixel );
	}

	/**
	 *	Scales image up to a minimum size if smaller than limit.
	 *	@access		public
	 *	@param		integer		$width		Minimum width
	 *	@param		integer		$height		Minimum height
	 *	@param		boolean		$interpolate	Flag: use interpolation
	 *	@param		integer		$maxMegaPixel	Maxiumum megapixels
	 *	@return		object		Processor object for chaining
	 *	@throws		OutOfRangeException if resulting image has more mega pixels than allowed
	 */
	public function scaleUpToLimit( $width, $height, $interpolate = TRUE, $maxMegaPixel = 50 ){
		if( !is_int( $width ) )
			throw new \InvalidArgumentException( 'Width must be integer' );
		if( !is_int( $height ) )
			throw new \InvalidArgumentException( 'Height must be integer' );
		$sourceWidth	= $this->image->getWidth();
		$sourceHeight	= $this->image->getHeight();
		if( $sourceWidth >= $width && $sourceHeight >= $height )
			return $this;
		$scale	= 1;
		if( $sourceWidth < $width )
			$scale	*= $width / $sourceWidth;
		if( $sourceHeight * $scale < $height )
			$scale	*= $height / ( $sourceHeight * $scale );
		$width	= (int) round( $sourceWidth * $scale );
		$height	= (int) round( $sourceHeight * $scale );
		if( $this->maxMegaPixels && $width * $height > $this->maxMegaPixels * 1024 * 1024 )
			throw new \OutOfRangeException( 'Larger than '.$this->maxMegaPixels.'MP ('.$width.'x'.$height.')' );
		return $this->resize( $width, $height, $interpolate, $maxMegaPixel );
	}

	/**
	 *	Scale image to fit into a size range.
	 *	Reduces to maximum size after possibly enlarging to minimum size.
	 *	Range maximum has higher priority.
	 *	For better resolution this method will first maximize and than minimize if both is needed.
	 *	@access		public
	 *	@param		integer		$minWidth		Minimum width
	 *	@param		integer		$minHeight		Minimum height
	 *	@param		integer		$maxWidth		Maximum width
	 *	@param		integer		$maxHeight		Maximum height
	 *	@param		boolean		$interpolate	Flag: use interpolation
	 *	@param		integer		$maxMegaPixel	Maxiumum megapixels
	 *	@return		object		Processor object for chaining
	 */
	public function scaleToRange( $minWidth, $minHeight, $maxWidth, $maxHeight, $interpolate, $maxMegaPixel = 50 ){
		$width	= $this->image->getWidth();
		$height	= $this->image->getHeight();
		if( $width < $minWidth || $height < $minHeight )
			return $this->scaleUpToLimit( $minWidth, $minHeight, $interpolate, $maxMegaPixel );
		else if( $width > $maxWidth || $height > $maxHeight )
			return $this->scaleDownToLimit( $maxWidth, $maxHeight, $interpolate, $maxMegaPixel );
		return $this;
	}
}
?>
