<?php
/**
 *	Image editor. Unfinished.
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
 *	Image editor. Unfinished.
 *	@category		Library
 *	@package		CeusMedia_Image
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2010-2015 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Image
 *	@todo			Code Doc
 */
class Editor{

	public function __construct( \CeusMedia\Image\Image $image ){
		$this->image	= $image;
		$this->processor	= new \CeusMedia\Image\Processor( $this->image );
		$this->filter		= new \CeusMedia\Image\Filter( $this->image );
	}

	public function overlay\CeusMedia\Image\Image $image, $alpha = 100 ){
		if( !is_int( $alpha ) )
			throw new \InvalidArgumentException( 'Alpha must be an integer' );
		$alpha	= min( 100, max( 0, $alpha ) );
		if( $alpha	== 0 )
			return;
		imagecopymerge(
			$this->image->getResource(),
			$image->getResource(),
			0, 0,																					//  start coordinates in main image
			0, 0,																					//  start coordinates in layer image
			$this->image->getWidth(),																//  width of resulting image
			$this->image->getHeight(),																//  height of resulting image
			$alpha																					//  opacity
		);
	}
}
?>
