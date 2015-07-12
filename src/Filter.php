<?php
/**
 *	Image filter.
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
 *	Image filter.
 *	@category		Library
 *	@package		CeusMedia_Image
 *	@uses			\CeusMedia\Image\Image
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2010-2015 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Image
 *	@see			http://www.php.net/manual/en/function.imagefilter.php
 *	@see			http://www.tuxradar.com/practicalphp/11/2/15
 */
class Filter{
	/**	@var		\CeusMedia\Image\Image		$resource		Image resource object */
	protected $image;

	public function __construct( \CeusMedia\Image\Image $image ){
		$this->image	= $image;
	}

	static public function apply( \CeusMedia\Image\Image $image, $filterName, $arguments = array() ){
		$filter		= new self( $image );
		if( !method_exists( $filter, $filterName ) )
			throw new \OutOfRangeException( 'Invalid filter "'.$filterName.'"' );
		\Alg_Object_MethodFactory::callObjectMethod( $filter, $filterName, $arguments );
	}

	/**
	 *	Blurs the image using the Gaussian method.
	 *	@access		public
	 *	@return		boolean
	 */
	public function blurGaussian(){
		return imagefilter( $this->image->getResource(), IMG_FILTER_GAUSSIAN_BLUR );
	}

	/**
	 *	Blurs the image.
	 *	@access		public
	 *	@return		boolean
	 */
	public function blurSelective(){
		return imagefilter( $this->image->getResource(), IMG_FILTER_SELECTIVE_BLUR );
	}

	/**
	 *	Changes the brightness of the image.
	 *	Values: -255 = min brightness, 0 = no change, +255 = max brightness
	 *	@access		public
	 *	@param		integer		$level		Value between -255 and 255
	 *	@return		boolean
	 */
	public function brightness( $level ){
		return imagefilter( $this->image->getResource(), IMG_FILTER_BRIGHTNESS, $level );
	}

	/**
	 *	Adds or subtracts colors.
	 *	@access		public
	 *	@param		integer		$red		Red component, value between -255 and 255
	 *	@param		integer		$red		Green component, value between -255 and 255
	 *	@param		integer		$red		Blue component, value between -255 and 255
	 *	@param		integer		$alpha		Alpha channel, value between 0 (opacue) and 127 (transparent)
	 *	@return		boolean
	 */
	public function colorize( $red, $green, $blue, $alpha = 0 ){
		return imagefilter( $this->image->getResource(), IMG_FILTER_COLORIZE, $red, $green, $blue, $alpha );
	}

	/**
	 *	Changes the contrast of the image.
	 *	Values: -100 = min contrast, 0 = no change, +100 = max contrast
	 *	@access		public
	 *	@param		integer		$level		Value between -100 and 100
	 *	@return		boolean
	 */
	public function contrast( $level ){
		return imagefilter( $this->image->getResource(), IMG_FILTER_CONTRAST, -1 * $level );
	}

	public function gamma( $level ){
		return imagegammacorrect( $this->image->getResource(), 1.0, (double) $level );
	}

	/**
	 *	Uses edge detection to highlight the edges in the image.
	 *	@access		public
	 *	@return		boolean
	 */
	public function detectEdges(){
		return imagefilter( $this->image->getResource(), IMG_FILTER_EDGEDETECT );
	}

	/**
	 *	Embosses the image.
	 *	@access		public
	 *	@return		boolean
	 */
	public function emboss(){
		return imagefilter( $this->image->getResource(), IMG_FILTER_EMBOSS );
	}

	/**
	 *	Converts the image into grayscale.
	 *	@access		public
	 *	@return		boolean
	 */
	public function grayscale(){
		return imagefilter( $this->image->getResource(), IMG_FILTER_GRAYSCALE );
	}

	/**
	 *	Reverses all colors of the image.
	 *	@access		public
	 *	@return		boolean
	 */
	public function negate(){
		return imagefilter( $this->image->getResource(), IMG_FILTER_NEGATE );
	}

	/**
	 *	Applies pixelation effect to the image.
	 *	Attention: This method seem to be not working, at least for me. There method pixelate2 exists.
	 *	@access		public
	 *	@param		integer		$size		Block size in pixels
	 *	@param		boolean		$effect		Flag: activate advanced pixelation effect
	 *	@return		boolean
	 */

	public function pixelate( $size, $effect = FALSE ){
		return imagefilter( $this->image->getResource(), IMG_FILTER_PIXELATE, $size, $effect );
	}

	/**
	 *	Applies pixelation effect to the image.
	 *	This is a fix for not working IMG_FILTER_PIXELATE and implements mode GD_PIXELATE_UPPERLEFT only.
	 *	@access		public
	 *	@param		integer		$sizeX		Block width in pixels
	 *	@param		integer		$sizeY		Block height in pixels
	 *	@return		void
	 */
	public function pixelate2( $sizeX = 20, $sizeY = 20){
		if( !is_int( $sizeX ) || $sizeX < 1 )
			throw new \InvalidArgumentException( 'SizeX must be integer and atleast 1' );
		if( !is_int( $sizeY ) || $sizeY < 1 )
			throw new \InvalidArgumentException( 'SizeY must be integer and atleast 1' );
		if( $sizeX == 1 && $sizeY == 1 )
			throw new \InvalidArgumentException( 'One of the pixel sizes must differ from 1ss' );
		$width	= $this->image->getWidth();
		$height	= $this->image->getHeight();
		$image	= $this->image->getResource();
		for( $y=0; $y<$height; $y+=$sizeY ){
			for( $x=0; $x<$width; $x+=$sizeX ){
				$rgb	= imagecolorsforindex( $image, imagecolorat( $image, $x, $y ) );			//  get the color for current pixel
				$color	= imagecolorclosest( $image, $rgb['red'], $rgb['green'], $rgb['blue'] );	//  get the closest color from palette
				imagefilledrectangle( $image, $x, $y, $x+$sizeX-1, $y+$sizeY-1, $color);			//  fill block
			}
		}
	}

	/**
	 *	Uses mean removal to achieve a "sketchy" effect.
	 *	@access		public
	 *	@return		boolean
	 */
	public function removeMean()
	{
		return imagefilter( $this->image->getResource(), IMG_FILTER_MEAN_REMOVAL );
	}

	public function sepia(){

		imagefilter( $this->image->getResource(), IMG_FILTER_GRAYSCALE );
		imagefilter( $this->image->getResource(), IMG_FILTER_BRIGHTNESS, -30 );
		imagefilter( $this->image->getResource(), IMG_FILTER_COLORIZE, 90, 55, 30 );
	}

	/**
	 *	Makes the image smoother.
	 *	Applies a 9-cell convolution matrix where center pixel has the weight arg1 and others weight of 1.0.
	 *	The result is normalized by dividing the sum with arg1 + 8.0 (sum of the matrix).
	 *	Any float is accepted, large value (in practice: 2048 or more) = no change
	 *	@access		public
	 *	@param		integer		$weight		Level of smoothness
	 *	@return		boolean
	 */
	public function smooth( $weight )
	{
		return imagefilter( $this->image->getResource(), IMG_FILTER_SMOOTH, $weight );
	}
}
?>
