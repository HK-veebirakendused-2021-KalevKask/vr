<?php
	require_once "usesession.php";
	require_once "../../../conf.php";
	require_once "fnc_general.php";
	require_once "fnc_photo.php";
	
	$photo_upload_error = null;
	$image_file_type = null;
	$image_file_name = null;
	$file_name_prefix = "vr_";
	$file_size_limit = 1 * 1024 * 1024;
	$image_max_w = 600;
	$image_max_h = 400;
	
	$image_height = 1500;
	$image_width = 1500;
	
	//var_dump($_POST);
	//var_dump($_SESSION);
	//var_dump($_FILES);
	
	
	if(isset($_POST["photo_submit"])){
		
		//var_dump($_FILES);
		//kas üldse on pilt
		$check = getimagesize($_FILES["file_input"]["tmp_name"]);
		if($check !== false){ //kontollime kas tegemist on pildi failiga
			//kontollime kas akspeteeritud faili vorming ja fikseerime laiendi
			if($check["mime"] == "image/jpeg"){
				$image_file_type = "jpg";
			}elseif($check["mime"] == "image/png"){
				$image_file_type = "png";
			}else{
				$photo_upload_error = "pole sobiv formaat! ainult jpg ja png on lubatud";
			}
		} else {
				$photo_upload_error = "tegemist pole pildifailiga";
		}
		 
		//Pildi mõõtmed peavad olema numbrid ja suuremad kui 0
		if((!is_numeric($_POST["image_height_input"]))||(!is_numeric($_POST["image_width_input"]))|| $_POST["image_height_input"] <= 0 || $_POST["image_width_input"] <=0){
			$photo_upload_error .= "Sisestatud pildi mõõtmed pole korrektsed";
		}
		
		if(empty($photo_upload_error)){ //kui errorit ei tekkinud, kontrollime faili suurust
			//ega pole liiga suur faili
			if($_FILES["file_input"]["size"] > $file_size_limit){
				$photo_upload_error = "Valitud fail on liiga suur, lubatud kuni 1MB!";
				}
				
			if(empty($photo_upload_error)){//
				//loome oma failinime
				$timestamp = microtime(1) * 10000;
				$image_file_name = $file_name_prefix .$timestamp ."." .$image_file_type;
				
				//suuruse muutmine
				// Vaja teha pildi failist piksli kogu, ehk image objekt
				$temp_image = null;
				if($image_file_type == "jpg"){
					$temp_image = imagecreatefromjpeg($_FILES["file_input"]["tmp_name"]);
				}
				if($image_file_type == "png"){
					$temp_image = imagecreatefrompng($_FILES["file_input"]["tmp_name"]);
				}
				$new_temp_image = resize_image($temp_image, $_POST["image_height_input"], $_POST["image_width_input"], $_POST["crop_input"]);
				$thumbnail_temp_image = resize_image($temp_image, 100, 100, $_POST["crop_input"]);//väikse pildi tegemine
				$target_file = "../upload_photos_normal/" .$image_file_name;
				$target_thumbnail = "../upload_photos_thumbnail/" .$image_file_name;
				if($image_file_type == "jpg"){
					if(imagejpeg($new_temp_image, $target_file, 90)){//lõpus 90 on kvaliteedi määraja
						$photo_upload_error = " Vähendatud pilt on salvestatud";
						imagejpeg($thumbnail_temp_image, $target_thumbnail, 90);//salvestab väikese pildi
						imagedestroy($new_temp_image);
						imagedestroy($thumbnail_temp_image);
					}else {
						$photo_upload_error = " Vähendatud pilti ei salevstatud";
						imagedestroy($new_temp_image);
						imagedestroy($thumbnail_temp_image);						
					}
				}
				
				if($image_file_type == "png"){
					if(imagepng($new_temp_image, $target_file, 6)){//lõpus 90 on kvaliteedi määraja
						$photo_upload_error = " Vähendatud pilt on salvestatud";
						imagejpeg($thumbnail_temp_image, $target_thumbnail, 90);//salvestab väikese pildi
						imagedestroy($new_temp_image);
						imagedestroy($thumbnail_temp_image);						
					}else {
						$photo_upload_error = " Vähendatud pilti ei salevstatud";	
						imagedestroy($new_temp_image);
						imagedestroy($thumbnail_temp_image);
					}
				}
				
				//$target_file = "../upload_photos_orig/" .$_files["file_input"]["name"]; //muutaja mise leiab ajutiselt üles laetud pildi asukoha
				$target_file = "../upload_photos_orig/" .$image_file_name; //lisasime üles lateva pildi nimeks kuju mis on all pool kirjeldatud ja minu poolt muudetud kujul
				//if(file_exists($target_file))
				if(move_uploaded_file($_FILES["file_input"]["tmp_name"], $target_file)){ //väidet kontollides tõstetakse ajutine pilt ümber target kataloogi kui õnnestub
					$photo_upload_error .= " Foto üleslaadimine õnnestus!";
					//panema pildi andmed andmebaasi kuna pilt sai üles laetud
					$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]); //sisaldab conf failis olevaid parameetreid
					//määrame suhtluseks kodeeringu
					$conn -> set_charset("utf8");
					// valmistan ette SQL käsu
					$stmt = $conn -> prepare("INSERT INTO vr21_photos (vr21_photos_userid, vr21_photos_filename, vr21_photos_origname, vr21_photos_alttext, vr21_photos_privacy) VALUES (?,?,?,?,?)"); // testime kas funktsioon töötab, sellpärast on valus väärtuseks kolm küsimärki
					echo $conn -> error; //kui on näiteks andmetabeli väli kirjutatud valesti saame siit kätte info. Tegemist on testiga, mitte veel päris andmete saatmisega
					//Nüüd saadame päriselt andmed
					// ?-ga andmete sidumine i-integer, s-string d-decimal, peavad ühtima väljadega
					$stmt -> bind_param("isssi", $_SESSION["user_id"],$image_file_name, $_FILES["file_input"]["name"], $_POST["alt_text"], $_POST["privacy_input"] ); /// ette on kirjutatud kolm s-i, ehk eelevalt kontrollitakse kas antud sisendi andmetüübid vastavad sellele mida andmbaas ootab. Järgnevad sisendid peavad olema samas järjekorras nagu prepare käsus
					$stmt -> execute(); //täitetakse käsk
					$stmt -> close();
					$conn -> close(); //logitakse andmebaasist välja

				}else{ //kui ei õnnestu antakse veateada
					$photo_upload_error .= " Foto laadimine ebaõnnestus!";
				}
			}
		}
	}
	

	
	
?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	
	<title> Veebirakendused ja nende loomine </title>
</head>
<body>
	<h1>Fotode üleslaadimine</h1>
	<p>See leht on valminud õppetöö raames!</p>
	<hr>
	<p><a href="?logout=1">Logi välja</a></p>
	<p><a href="home.php">Avalehele</a></p>
	<hr>
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
		<label for="file_input">Vali foto endale: </label>
		<input id="file_input" name="file_input" type="file">
		<br>
		<label for="alt_input">Alternatiivtekst ehk pildi selgitus: </label>
		<input id="alt_text" name="alt_text" type="text" placeholder="Pildil on ...">
		<br>
		<label>Privaatsustase: </label>
		<br>
		<input id="privacy_input_1" name="privacy_input" type="radio" value="3" checked>
		<label for="privacy_input_1">Privaatne</label>
		<br>
		<input id="privacy_input_2" name="privacy_input" type="radio" value="2">
		<label for="privacy_input_2">Registreeritud kasutajatele</label>
		<br>
		<input id="privacy_input_3" name="privacy_input" type="radio" value="1">
		<label for="privacy_input_3">Avalik</label>
		<br>
		<label>Pildi üles laadimise suurus:</label>
		<br>
		<input name="image_height_input" type="text" value="<?php echo $image_height; ?>"><!--<span><//?php echo $height_error; ?></span><br> -->
		<label>kõrgus pikslites</label>
		<br>
		<input name="image_width_input" type="text" value="<?php echo $image_width; ?>">
		<label>laius pikslites</label>
		<br> 
		<input id="crop_input_1" name="crop_input" type="radio" value="0" checked>
		<label for="crop_input_1">Luban pilti lõigata (Sisned võetakse raami suuruseks mille sisu täitetakse pildiga)</label>
		<br>
		<input id="crop_input_2" name="crop_input" type="radio" value="1">
		<label for="crop_input_2">Ei luba pilti lõigata (Sisend võteakse pildi äärte maksimaalseteks suurusteks)</label>
		<br>

	

		<br>
		<input type="submit" name="photo_submit" value="Lae pilt üles!">
		<br>
		

		<br>
	</form>
	<p><?php echo $photo_upload_error; ?></p>
</body>
</html>
	