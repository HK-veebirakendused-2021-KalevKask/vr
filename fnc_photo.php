<?php


	$rezised_image = null;
	$notice = "";
	$result = 0;
	
	
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
			imagesavealpha($return_image, true);
			$trans_color = imagecolorallocatealpha($return_image, 0, 0, 0, 127);
			imagefill($return_image, 0, 0, $trans_color);
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
			imagesavealpha($return_image, true);
			$trans_color = imagecolorallocatealpha($return_image, 0, 0, 0, 127);
			imagefill($return_image, 0, 0, $trans_color);
			imagecopyresampled($return_image, $source_image, $start_x, $start_y, 0, 0, $image_new_w, $image_new_h, $image_w, $image_h);
			return $return_image;
			
		}
	}
	
	function display_gallery($privacy, $source){
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		//määrame suhtluseks kodeeringu
		$conn -> set_charset("utf8");
		//valmistan ette SQL käsu
		$stmt = $conn -> prepare("SELECT vr21_photos.vr21_photos_id, vr21_photos.vr21_photos_filename, vr21_photos.vr21_photos_alttext, vr21_users.vr21_users_firstname, vr21_users.vr21_users_lastname FROM vr21_photos JOIN vr21_users ON vr21_photos.vr21_photos_userid = vr21_users.vr21_users_id WHERE vr21_photos.vr21_photos_privacy <= ? AND vr21_photos.vr21_photos_deleted IS NULL GROUP BY vr21_photos.vr21_photos_id"); //kilejdame ära mida tahame lugeda ja kust tabelist
		//$stmt -> bind_result($photos_id_from_db, $photos_filename_from_db, $photos_alttext_from_db, $users_firstname_from_db, $users_lastname_from_db); //lisame andmed andmbaasist uutesse muutujatesse
		//i - integer   s - string   d - decimal
		echo $conn -> error;
		$stmt -> bind_param("i", $privacy); // edastame uudiste arvu SQL-käsule
		$stmt -> bind_result($photo_id_from_db, $photo_filename_from_db, $photo_alttext_from_db, $user_firstname_from_db, $user_lastname_from_db);
		$stmt -> execute();
		$display_gallery = "";
		while ($stmt -> fetch()) {
			$display_gallery .= '<div class="Gallery">';
			$display_gallery .= '<img src="' .$source.$photo_filename_from_db. '" alt="' .$photo_alttext_from_db .'" class="thumb" data-fn="pildifaili_nimi" data-id="pildi_id">';
			$display_gallery .= '<p>'.$user_firstname_from_db ." " .$user_lastname_from_db .'</p></div>';

		}
		$stmt -> close();
		$conn -> close();
		return $display_gallery;
	}
	
	
