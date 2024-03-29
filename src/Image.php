<?php
/**
 *	Image resource reader and writer.
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

use Exception;
use InvalidArgumentException;
use RuntimeException;

/**
 *	Image resource reader and writer.
 *	@category		Library
 *	@package		CeusMedia_Image
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2010-2015 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Image
 *	@todo			Code Doc
 */
/*
 Types:
 ------
 0 - UNKNOWN	IMAGETYPE_UNKNOWN
 1 - GIF		IMAGETYPE_GIF
 2 - JPEG		IMAGETYPE_JPEG
 3 - PNG		IMAGETYPE_PNG
 4 - SWF		IMAGETYPE_SWF
 5 - PSD		IMAGETYPE_PSD
 6 - BMP		IMAGETYPE_BMP
 7 - TIFF_II	IMAGETYPE_TIFF_II
 8 - TIFF_MM	IMAGETYPE_TIFF_MM
 9 - JPC		IMAGETYPE_JPC
 9 - JPEG2000	IMAGETYPE_JPEG2000
10 - JP2		IMAGETYPE_JP2
11 - JPX		IMAGETYPE_JPX
12 - JB2		IMAGETYPE_JB2
14 - IFF		IMAGETYPE_IFF
15 - WBMP		IMAGETYPE_WBMP
16 - XBM		IMAGETYPE_XBM
17 - ICO		IMAGETYPE_ICO
*/
class Image
{
	/** @var resource|NULL $resource */
	protected $resource			= NULL;

	/** @var integer $type */
	protected $type				= IMAGETYPE_PNG;

	/** @var integer $width */
	protected $width			= 0;

	/** @var integer $height */
	protected $height			= 0;

	/** @var integer $quality */
	protected $quality			= 100;

	/** @var string|NULL $fileName */
	protected $fileName			= NULL;

	/** @var int|NULL $colorTransparent */
	public $colorTransparent;

	public function __clone()
	{
		if( !$this->resource )
			return;
		$copy	= imagecreatetruecolor( $this->width, $this->height );
		imagecopy( $copy, $this->resource, 0, 0, 0, 0, $this->width, $this->height );
		$this->resource = $copy;
	}

	public function __construct( string $fileName = NULL, bool $tolerateAnimatedGif = FALSE )
	{
		if( !is_null( $fileName ) )
			$this->load( $fileName, $tolerateAnimatedGif );
	}

	public function __toString()
	{
		ob_start();
		$this->display( FALSE );
		$data		= ob_get_clean();
		$mimeType	= image_type_to_mime_type( $this->type );
		return 'data:'.$mimeType.';base64,'.base64_encode( $data );
	}

	/**
	 *	Creates a new image resource.
	 *	@access		public
	 *	@param		integer		$width		Width of image
	 *	@param		integer		$height		Height of image
	 *	@param		boolean		$trueColor	Flag: create an TrueColor Image (24-bit depth and without fixed palette)
	 *	@return		void
	 */
	public function create( $width, $height, $trueColor = TRUE )
	{
		$resource		= $trueColor ? imagecreatetruecolor( $width, $height ) : imagecreate( $width, $height );
		if( $resource ){
			$this->type		= $trueColor ? IMAGETYPE_PNG : IMAGETYPE_GIF;
			$this->width	= $width;
			$this->height	= $height;
			$this->setResource( $resource );
		}
	}

	public function display( bool $sendContentType = TRUE ): void
	{
		if( $sendContentType )
			header( 'Content-type: '.$this->getMimeType() );
		switch( $this->getType() ){
			case IMAGETYPE_GIF:
				imagegif( $this->resource );
				break;
			case IMAGETYPE_JPEG:
				imagejpeg( $this->resource, NULL, $this->quality );
				break;
			case IMAGETYPE_PNG:
				imagepng( $this->resource );
				break;
			default:
				header_remove( 'Content-type' );
				new Error( 'invalid type' );
		}
	}

	public function getColor( int $red, int $green, int $blue, int $alpha = 0 ): int
	{
		return imagecolorallocatealpha( $this->resource, $red, $green, $blue, $alpha );
	}

	/**
	 *	...
	 *	@access		public
	 *	@return		string|NULL
	 */
	public function getFileName(): ?string
	{
		return $this->fileName;
	}

	/**
	 *	Returns height.
	 *	@access		public
	 *	@return		integer
	 */
	public function getHeight()
	{
		return $this->height;
	}

	/**
	 *	Returns MIME type of image.
	 *	@access		public
	 *	@return		string
	 */
	public function getMimeType()
	{
		return image_type_to_mime_type( $this->type );
	}

	/**
	 *	Returns quality.
	 *	@access		public
	 *	@return		integer
	 */
	public function getQuality()
	{
		return $this->quality;
	}

	/**
	 *	Returns inner image resource.
	 *	@access		public
	 *	@return		resource
	 */
	public function getResource()
	{
		return $this->resource;
	}

	/**
	 *	Returns image type.
	 *	@access		public
	 *	@return		integer
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 *	Returns width.
	 *	@access		public
	 *	@return		integer
	 */
	public function getWidth()
	{
		return $this->width;
	}

	/**
	 *	Indicates whether an Image File is an animated GIF.
	 *	@access		public
	 *	@static
	 *	@param		string		$filePath	Path Name of Image File
	 *	@return		boolean		TRUE if Image File is an animated GIF
	 */
	public static function isAnimated( $filePath )
	{
		$content	= file_get_contents( $filePath );
		$pos1		= 0;
		$count		= 0;
		while( $count < 2 ){																		//  There is no point in continuing after we find a 2nd frame
			$pos1	= strpos( $content, "\x00\x21\xF9\x04", $pos1 );
			if( $pos1 === FALSE )
				break;
			$pos2	= strpos( $content, "\x00\x2C", $pos1 );
			if( $pos2 === FALSE )
				break;
			else if( $pos1 + 8 == $pos2 )
				$count++;
			$pos1 = $pos2;
		}
		return $count > 1;
	}

	/**
	 *	Reads an image from file, supporting several file types.
	 *	@access		public
	 *	@param		string		$fileName				Name of image file
	 *	@param		boolean		$tolerateAnimatedGif	Flag: ???
	 *	@return		void
	 *	@throws		RuntimeException if file is not existing
	 *	@throws		RuntimeException if file is not readable
	 *	@throws		RuntimeException if file is not a image
	 *	@throws		Exception if detected image type is not supported
	 *	@throws		Exception if image type is not supported for reading
	 */
	public function load( $fileName, bool $tolerateAnimatedGif = FALSE )
	{
		if( !file_exists( $fileName ) )
			throw new RuntimeException( 'Image "'.$fileName.'" is not existing' );
		if( !is_readable( $fileName ) )
			throw new RuntimeException( 'Image "'.$fileName.'" is not readable' );
		$info = getimagesize( $fileName );
		if( !$info )
			throw new Exception( 'Image "'.$fileName.'" is not of a supported type' );
		if( !$tolerateAnimatedGif && self::isAnimated( $fileName ) )
			throw new RuntimeException( 'Animated GIFs are not supported' );
		if( $this->resource )
			imagedestroy( $this->resource );
		$this->type		= $info[2];
		switch( $this->type ){
			case IMAGETYPE_GIF:
				$resource	= imagecreatefromgif( $fileName );
				break;
			case IMAGETYPE_JPEG:
				$resource	= imagecreatefromjpeg( $fileName );
				break;
			case IMAGETYPE_PNG:
				$resource	= imagecreatefrompng( $fileName );
				break;
			default:
				throw new Exception( 'Image type "'.$info['mime'].'" is no supported, detected '.$info[2] );
		}
		if( $resource ){
			$this->fileName	= $fileName;
			$this->setResource( $resource );
		}
	}

	/**
	 *	Writes an image to file.
	 *	@access		public
	 *	@param		string		$fileName		Name of new image file
	 *	@param		integer		$type			Type of image (IMAGETYPE_GIF|IMAGETYPE_JPEG|IMAGETYPE_PNG)
	 *	@return		boolean
	 *	@throws		RuntimeException	if neither file has been loaded before nor a file name is given
	 *	@throws		Exception			if image type is not supported for writing
	 */
	public function save( $fileName = NULL, $type = NULL )
	{
		$type		= $type ?? $this->type;
		$fileName	= $fileName ?? $this->fileName;
		if( !$fileName )
			throw new RuntimeException( 'No image file name set' );
		switch( $type ){
			case IMAGETYPE_GIF:
				imagegif( $this->resource, $fileName );
				break;
			case IMAGETYPE_JPEG:
				imagejpeg( $this->resource, $fileName, $this->quality );
				break;
			case IMAGETYPE_PNG:
				imagepng( $this->resource, $fileName );
				break;
			default:
				throw new Exception( 'Image type "'.$type.'" is no supported' );
		}
		if( $fileName === $this->fileName )															//  if saved to same file
			$this->load( $this->fileName );															//  reload image
		return TRUE;
	}

	public function setQuality( int $quality ): self
	{
		$this->quality = max( 0, min( 100, $quality ) );
		return $this;
	}

	/**
	 *	Binds image resource to this image object.
	 *	@access		public
	 *	@param		resource	$resource		Image resource
	 *	@return		void
	 *	@thows		InvalidArgumentException
	 */
	public function setResource( $resource )
	{
		if( !is_resource( $resource ) )
			throw new InvalidArgumentException( 'Must be a valid image resource' );
		if( $this->resource )
			imagedestroy( $this->resource );

		$this->resource	= $resource;
		$this->width	= imagesx( $resource );
		$this->height	= imagesy( $resource );

		if( function_exists( 'imageantialias' ) )
			imageantialias( $this->resource, TRUE );

		imagealphablending( $this->resource, FALSE );												//  disable alpha blending in favour to
		imagesavealpha( $this->resource, TRUE );													//  copying the complete alpha channel
	}

/*	public function setTransparentColor( $red, $green, $blue, int $alpha = 0 )
	{
		$color	= imagecolorallocatealpha( $this->resource, $red, $green, $blue, $alpha );
		imagecolortransparent( $this->resource, $color );
	}*/

	public function setTransparentColor( int $color ): self
	{
		imagecolortransparent( $this->resource, $color );
		$this->colorTransparent	= $color;	
		return $this;
	}

	public function setType( int $type ): self
	{
		if( !( ImageTypes() & $type ) )
			throw new InvalidArgumentException( 'Invalid type' );
		$this->type	= $type;
		if( $this->fileName ){
			$baseName	= pathinfo( $this->fileName, PATHINFO_FILENAME );
			$pathName	= pathinfo( $this->fileName, PATHINFO_DIRNAME );
			$extension	= image_type_to_extension( $this->type );
			$this->fileName	= $pathName.'/'.$baseName.$extension;
		}
		return $this;
	}
}
