<?php
/**
 *	Basic Image Creation.
 *
 *	Copyright (c) 2007-2020 Christian Würker (ceusmedia.de)
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
 *	@package		CeusMedia_Common_UI_Image
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2007-2020 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Common
 */
namespace CeusMedia\Image;

use CeusMedia\Image\Image;

/**
 *	Basic Image Creation.
 *	@category		Library
 *	@package		CeusMedia_Common_UI_Image
 *	@uses			UI_Image_Printer
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2007-2020 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Common
 */
class Drawer
{
	/** @var Image		$image */
	protected $image;

	/** @var integer	$type */
	protected $type	= 0;

	/**
	 *	Constructor.
	 *	@access		public
	 *	@param		Image		$image		Image object with resource, can be created with Creator
	 *	@return		void
	 */
	public function __construct( Image $image )
	{
		$this->setImage( $image );
	}

	/**
	 *	@return		self
	 */
	public function drawBorder( int $color, int $width = 1 ): self
	{
		for( $i = 0; $i < $width; $i++ )
			$this->drawRectangle( 0 + $i, 0 + $i, imagesx( $this->image->getResource() ) - $i - 1, imagesy( $this->image->getResource() ) - $i - 1, $color );
		return $this;
	}

	public function drawLine( int $x0, int $y0, int $x1, int $y1, int $color ): self
	{
		imageline( $this->image->getResource(), $x0, $y0, $x1, $y1, $color );
		return $this;
	}

	public function drawPixel( int $x, int $y, int $color ): self
	{
		imagesetpixel( $this->image->getResource(), $x, $y, $color );
		return $this;
	}

	public function drawRectangle( int $x0, int $y0, int $x1, int $y1, int $color ): self
	{
		imagerectangle( $this->image->getResource(), $x0, $y0, $x1, $y1, $color );
		return $this;
	}

	public function drawString( int $x, int $y, string $text, int $size, int $color ): self
	{
		imagestring( $this->image->getResource(), $size, $x, $y, $text, $color );
		return $this;
	}

	public function fill( int $color ): self
	{
		imagefilledrectangle( $this->image->getResource(), 0, 0, imagesx( $this->image->getResource() ) - 1, imagesy( $this->image->getResource() ) - 1, $color );
		return $this;
	}

	public function fillRectangle( int $x0, int $y0, int $x1, int $y1, int $color ): self
	{
		imagefilledrectangle( $this->image->getResource(), $x0, $y0, $x1, $y1, $color );
		return $this;
	}

	public function getColor( int $red, int $green, int $blue, int $alpha = 0 ): int
	{
		return imagecolorallocatealpha( $this->image->getResource(), $red, $green, $blue, $alpha );
	}

	public function getImage(): Image
	{
		return $this->image;
	}

/*	public function isSet()
	{
		return isset( $this->image );
	}
*/
	/**
	 *	Sets Image Handler.
	 *	@access		public
	 *	@param		Image		$image		Image object with resource
	 *	@return		self
	 */
	public function setImage( Image $image ): self
	{
		$this->image = $image;
		return $this;
	}

	public function show( int $quality = 100 ): void
	{
		Printer::showImage( $this->image, $this->type, $quality );
		die;
	}
}
