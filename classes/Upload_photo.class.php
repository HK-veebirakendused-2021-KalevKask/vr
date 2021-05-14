<?php
	class Upload_photo {
		private $photo_to_upload;
		private $image_file_type;
		private $temp_image;
		public $new_temp_image; //hiljem, kui classhakkab kõike isetegema, siis ilmsel private
		
		function __construct($photo_to_upload, $image_file_type){
			$this->photo_to_upload = $photo_to_upload;
			$this->image_file_type = $image_file_type;//see tuleks koha peal kindlaks teha
			$this->temp_image = $this->create_image_from_file(
			
		}
	}//class lõppeb