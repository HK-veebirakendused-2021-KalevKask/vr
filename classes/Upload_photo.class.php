
<?php
	require_once "../../../conf.php";
	require_once "usesession.php";
	
	class Upload_photo {
		private $photo_to_upload;
		private $post_data;
		public $image_file_type;
		private $temp_image;
		public $photo_upload_error;
		public $new_temp_image; //hiljem, kui class hakkab kõike ise tegema siis ilmselt private
		private $check;
		public $image_file_name;
		
		
		function __construct($photo_to_upload, $post_data){
			$this->photo_to_upload = $photo_to_upload;
			$this->post_data = $post_data;			
			//ka test, kas on ülkdse pilt ja sobiv, peaks siin klassis olema
			$this->temp_image = $this->create_image_from_file($this->photo_to_upload["tmp_name"], $this->image_file_type);
			$this->check = getimagesize($this->photo_to_upload["tmp_name"]);
			if($this->check["mime"] == "image/jpeg"){
				$this->image_file_type = "jpg";
			}
			if($this->check["mime"] == "image/png"){
				$this->image_file_type = "png";
			}
			$this->temp_image = $this->create_image_from_file($this->photo_to_upload["tmp_name"], $this->image_file_type);
			 // see tuleb edaspidi klassi koha peal kindlaks teha
			
		}
		
		
		
		
		function __destruct(){
			if(isset($this->new_temp_image)){
				@imagedestroy($this->new_temp_image);
			}
			if(isset($this->temp_image)){
				imagedestroy($this->temp_image);
			}
		}

		public function file_type_check($file_size_limit){ //Kontrollitakse, kas faili suurus, faili tüüp ja soovitud faili salvestamise formaat on õige
			if($this->check !== false){ //kontollime kas tegemist on pildi failiga
				//kontollime kas akspeteeritud faili vorming ja fikseerime laiendi
				if($this->check["mime"] == "image/jpeg"){
					$this->image_file_type = "jpg";
				}elseif($this->check["mime"] == "image/png"){
					$this->image_file_type = "png";
				}else{
					$this->photo_upload_error = "Pole sobiv formaat! ainult jpg ja png on lubatud";
				}
			} else {
				$this->photo_upload_error = "Tegemist pole pildifailiga";
			}
		 
			//Pildi mõõtmed peavad olema numbrid ja suuremad kui 0
			if((!is_numeric($this->post_data["image_height_input"]))||(!is_numeric($this->post_data["image_width_input"]))|| $this->post_data["image_height_input"] <= 0 || $this->post_data["image_width_input"] <=0){
				$this->photo_upload_error .= "Sisestatud pildi mõõtmed pole korrektsed";
			}
			
			if(empty($this->photo_upload_error)){ //kui errorit ei tekkinud, kontrollime faili suurust
				//ega pole liiga suur faili
				if($this->photo_to_upload["size"] > $file_size_limit){
					$this->photo_upload_error = "Valitud fail on liiga suur, lubatud kuni 1MB!";
					}
			}
			return $this->photo_upload_error;
		}
				

		
		private function create_image_from_file($image, $image_file_type){ // teeme piksli kogumi
			$temp_image = null;
			if($image_file_type == "jpg"){
				$temp_image = imagecreatefromjpeg($image);
			}
			if($image_file_type == "png"){
				$temp_image = imagecreatefrompng($image);
			}
			return $temp_image;
		}
		
		public function resize_image($width_image, $height_image, $crop_image){
			$image_w = imagesx($this->temp_image);
			$image_h = imagesy($this->temp_image);
			//Kontrollin kas soovitakse kuvasuhet säilitada
			if($crop_image == 1){
				//kuvasuhte säilitamiseks arvutame suuruse muutuse kordaja lähtudes kõrgusest või laiusest
				if($image_w / $width_image > $image_h / $width_image){
					$image_size_ratio = $image_w / $width_image;
				}else{
					$image_size_ratio = $image_h / $height_image;
				}
				
				$image_new_w = round($image_w / $image_size_ratio);
				$image_new_h = round($image_h / $image_size_ratio);
				
				//vähendamiseks loome uue image objekti, kuhu kopeerimine vähendatud kujutise
				$this->new_temp_image = imagecreatetruecolor($image_new_w, $image_new_h); //teeme tühja pildi kirjeldatud suurusega
				imagesavealpha($this->new_temp_image, true);
				$trans_color = imagecolorallocatealpha($this->new_temp_image, 0, 0, 0, 127);
				imagefill($this->new_temp_image, 0, 0, $trans_color);
				imagecopyresampled($this->new_temp_image, $this->temp_image, 0, 0, 0, 0, $image_new_w, $image_new_h, $image_w, $image_h);
				
			}else{//Kui soovitakse pilt vastavalt välja lõigata
			
				$start_x = 0;
				$start_y = 0;
				
				//kuvasuhte säilitamiseks arvutame suuruse muutuse kordaja lähtudes kõrgusest või laiusest
				if($image_w / $width_image < $image_h / $height_image){
					$image_new_w = $width_image;
					$image_new_h = round($image_h/($image_w / $image_new_w));
					$start_y = ($height_image - $image_new_h) / 2; 
					
				}else{
					$image_new_h = $height_image;
					$image_new_w = round($image_w/($image_h / $image_new_h));
					$start_x = ($width_image - $image_new_w) / 2; 
				}
				
			
				
				$this->new_temp_image = imagecreatetruecolor($width_image, $height_image); //teeme tühja pildi kirjeldatud suurusega
				imagesavealpha($this->new_temp_image, true);
				$trans_color = imagecolorallocatealpha($this->new_temp_image, 0, 0, 0, 127);
				imagefill($this->new_temp_image, 0, 0, $trans_color);
				imagecopyresampled($this->new_temp_image, $this->temp_image, $start_x, $start_y, 0, 0, $image_new_w, $image_new_h, $image_w, $image_h);
		
			}
		}
		
		public function save_photo($target_file){
			if($this->image_file_type == "jpg"){
				if(imagejpeg($this->new_temp_image, $target_file, 90)){//lõpus 90 on kvaliteedi määraja
					$result = 1; //Pilt salvestati
				}else {
					$result = 0; //Pilti ei salvestatud						
				}
			}
		
			if($this->image_file_type == "png"){
				if(imagepng($this->new_temp_image, $target_file, 6)){//lõpus 90 on kvaliteedi määraja
					$result = 1; //Pilt salvestati				
				}else {
					$result = 0; //Pilti ei salvestatud	
				}
			}
			imagedestroy($this->new_temp_image);
			
			return $result;
		}
		
		public function add_watermark($watermark){
			$watermark_file_type = strtolower(pathinfo($watermark, PATHINFO_EXTENSION));
			$watermark_image = $this->create_image_from_file($watermark, $watermark_file_type);
			$watermark_w = imagesx($watermark_image);
			$watermark_h = imagesy($watermark_image);
			$watermark_x = imagesx($this->new_temp_image) - $watermark_w - 10;
			$watermark_y = imagesy($this->new_temp_image) - $watermark_h - 10;
			imagecopy($this->new_temp_image, $watermark_image, $watermark_x, $watermark_y, 0, 0, $watermark_w, $watermark_h);
			imagedestroy($watermark_image);
				
		}
		
		public function image_file_name($name_isset){
			if(($name_isset) !== ""){
				$image_file_name = "vr_" .$_POST["save_name"] ."." . $this->image_file_type;
			}else{
				//loome oma failinime
				$timestamp = microtime(1) * 10000;
				$image_file_name = "vr_" .$timestamp ."." . $this->image_file_type;
			}
			return $image_file_name;
		}
		
		public function orig_image_upload($orig_fail, $target){
			if(move_uploaded_file($orig_fail, $target)){
				return True;
			}else{
				return False;
			}
		}
	} //class lõppeb