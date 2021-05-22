<?php


	$rezised_image = null;
	
	function resize_image($source_image, $width_image, $height_image, $crop_image){
		$image_w = imagesx($source_image);
		$image_h = imagesy($source_image);
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
			$return_image = imagecreatetruecolor($image_new_w, $image_new_h); //teeme tühja pildi kirjeldatud suurusega
			imagecopyresampled($return_image, $source_image, 0, 0, 0, 0, $image_new_w, $image_new_h, $image_w, $image_h);
			return $return_image;
			
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
			
		
			
			$return_image = imagecreatetruecolor($width_image, $height_image); //teeme tühja pildi kirjeldatud suurusega
			imagecopyresampled($return_image, $source_image, $start_x, $start_y, 0, 0, $image_new_w, $image_new_h, $image_w, $image_h);
			return $return_image;
			
		}
	}