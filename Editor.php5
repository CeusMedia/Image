<?php
class CMM_IMG_Editor{
	
	public function __construct( CMM_IMG_Image $image ){
		$this->image	= $image;
		$this->processor	= new CMM_IMG_Processor( $this->image );
		$this->filter		= new CMM_IMG_Filter( $this->image );
	}


	public function overlay( CMM_IMG_Image $image, $alpha = 100 ){
		if( !is_int( $alpha ) )
			throw new InvalidArgumentException( 'Alpha must be an integer' );
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