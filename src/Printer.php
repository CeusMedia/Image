<?php
/**
 *	Prints an Image Resource into a File or on Screen.
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
 *	@since			16.06.2008
 */
namespace CeusMedia\Image;

/**
 *	Prints an Image Resource into a File or on Screen.
 *	@category		Library
 *	@package		CeusMedia_Common_UI_Image
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2007-2020 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Common
 *	@since			16.06.2008
 */
class Printer
{
	/**	@var		Image		$image		Image object with resource */
	protected $image;

	/**
	 *	Constructor.
	 *	@access		public
	 *	@param		Image		$image		Image object with resource
	 *	@return		void
	 */
	public function __construct( $image )
	{
		if( !is_resource( $image->getResource() ) )
			throw new \InvalidArgumentException( 'Given Image Resource is not a valid Resource.' );
		$this->image	= $image;
	}

	/**
	 *	Writes Image to File.
	 *	@access		public
	 *	@param		string		$fileName		Name of target Image File
	 *	@param		int			$type			Image Type
	 *	@param		int			$quality		JPEG Quality (1-100)
	 *	@return		void
	 */
	public function save( string $fileName, $type = \IMAGETYPE_PNG, $quality = 100 )
	{
		self::saveImage( $fileName, $this->image, $type, $quality );
	}

	/**
	 *	Saves an Image to File statically.
	 *	@access		public
	 *	@static
	 *	@param		string		$fileName		Name of target Image File
	 *	@param		Image		$image			Image object with resource
	 *	@param		int			$type			Image Type
	 *	@param		int			$quality		JPEG Quality (1-100)
	 *	@return		void
	 */
	public static function saveImage( string $fileName, Image $image, $type = \IMAGETYPE_PNG, $quality = 100 )
	{
		switch( $type )
		{
			case \IMAGETYPE_PNG:
				ImagePNG( $image->getResource(), $fileName );
				break;
			case \IMAGETYPE_JPEG:
				ImageJPEG( $image->getResource(), $fileName, $quality );
				break;
			case \IMAGETYPE_GIF:
				ImageGIF( $image->getResource(), $fileName );
				break;
			default:
				throw new \InvalidArgumentException( 'Invalid Image Type' );
		}
	}

	public static function toBase64( Image $image, int $type = \IMAGETYPE_PNG, int $quality = 100 ): string
	{
		$stream = fopen( 'php://temp', 'w' );
		stream_filter_append( $stream, 'convert.base64-encode', STREAM_FILTER_WRITE );
		switch( $type )
		{
			case \IMAGETYPE_PNG:
				$pngQuality	= (int) ( ceil( $quality / 10 ) - 1 );
				ImagePNG( $image->getResource(), $stream, $pngQuality );
				break;
			case \IMAGETYPE_JPEG:
				ImageJPEG( $image->getResource(), $stream, $quality );
				break;
			case \IMAGETYPE_GIF:
				ImageGIF( $image->getResource(), $stream );
				break;
			default:
				throw new \InvalidArgumentException( 'Invalid Image Type' );
		}
		rewind( $stream );
		return stream_get_contents( $stream );
	}

	/**
	 *	Print Image on Screen.
	 *	@access		public
	 *	@param		int			$type			Image Type
	 *	@param		int			$quality		JPEG Quality (1-100)
	 *	@param		bool		$sendHeader		Flag: set Image MIME Type Header
	 *	@return		void
	 */
	public function show( $type = \IMAGETYPE_PNG, $quality = 100, $sendHeader = TRUE )
	{
		self::showImage( $this->image, $type, $quality, $sendHeader );
	}

	/**
	 *	Prints an Image to Screen statically.
	 *	@access		public
	 *	@static
	 *	@param		Image		$image			Image object with resource
	 *	@param		int			$type			Image Type
	 *	@param		int			$quality		JPEG Quality (1-100)
	 *	@param		bool		$sendHeader		Flag: set Image MIME Type Header
	 *	@return		void
	 */
	public static function showImage( Image $image, $type = \IMAGETYPE_PNG, $quality = 100, $sendHeader = TRUE )
	{
		switch( $type )
		{
			case \IMAGETYPE_GIF:
				if( $sendHeader )
					header( "Content-type: image/gif" );
				ImageGIF( $image->getResource() );
				break;
			case \IMAGETYPE_JPEG:
				if( $sendHeader )
					header( "Content-type: image/jpeg" );
				ImageJPEG( $image->getResource(), "", $quality );
				break;
			case \IMAGETYPE_PNG:
				if( $sendHeader )
					header( "Content-type: image/png" );
				ImagePNG( $image->getResource() );
				break;
			default:
				throw new \InvalidArgumentException( 'Invalid Image Type' );
		}
	}
}
