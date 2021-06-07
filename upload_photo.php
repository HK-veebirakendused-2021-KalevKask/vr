<?php
	require_once "usesession.php";
	require_once "../../../conf.php";
	require_once "fnc_general.php";
	require_once "fnc_photo.php";
	require_once "classes/Upload_photo.class.php";
	
	$photo_upload_error = null;
	$image_file_type = null;
	$image_file_name = null;
	$file_name_prefix = "vr_";
	$file_size_limit = 1 * 1024 * 1024;
	$image_max_w = 600;
	$image_max_h = 400;
	$watermark = "../images/vr_watermark.png";
	
	$image_height = 1500;
	$image_width = 1500;
	
	//var_dump($_POST);
	//var_dump($_SESSION);
	//var_dump($_FILES);
	
	
	if(isset($_POST["photo_submit"])){
		
		$photo_upload = new Upload_photo($_FILES["file_input"], $_POST); //Kasutame pildi üles laadimiseks klassi
		$photo_upload_error = $photo_upload->file_type_check($file_size_limit); //Kontrollime kas faili tüüp sobib üles laadimiseks
			
		if(empty($photo_upload_error)){//
			
			$image_file_name = $photo_upload->image_file_name($_POST["save_name"]); //anname failie nime
			$target_file = "../upload_photos_normal/" .$image_file_name; //anname muudetava pildifaili salvestamiseks asukoha
			$target_thumbnail = "../upload_photos_thumbnail/" .$image_file_name; //thumnaili pildi salvestamise asukoht
			$target_orig = "../upload_photos_orig/" .$image_file_name; //originaal pildi salvestamise asukoht
			
			//pildi suuruse muutmine koos salvestamisega
			$photo_upload->resize_image($_POST["image_height_input"], $_POST["image_width_input"], $_POST["crop_input"]); //pildi suuruse muutmise funktsioon
			//lisan vesimärgi
			$photo_upload->add_watermark($watermark); //pildile pannakse juurde vesmärk

			// pildi suuruse muutmine ja salvestamine
			$result = $photo_upload->save_photo($target_file); //kontrollitakse kas fail sai üles laetud
			if($result == 1){
				$notice = "Vähendatud pilt laeti üles! ";
			}else{
				$photo_upload_error = "Vähendatud pilti ei salvestatud tekkis viga!";
			}
			
			//thumbnaili pildi tegemine ja salvestamine
			$photo_upload->resize_image(100, 100, $_POST["crop_input"]);//Thumbnail pildi fikseeritud parameetrid
			$result = $photo_upload->save_photo($target_thumbnail);
			if($result == 1){
				$notice = "Vähendatud pilt laeti üles! ";
			}else{
				$photo_upload_error = "Vähendatud pilti ei salvestatud tekkis viga!";
			}
			//$photo_upload->origimage_upload($_FILES["file_input"]["tmp_name"]
			
			
			$target_file = "../upload_photos_orig/" .$image_file_name; //lisasime üles laeteva pildi nimeks kuju mis on all pool kirjeldatud ja minu poolt muudetud kujule
			if($photo_upload->orig_image_upload($_FILES["file_input"]["tmp_name"], $target_orig)){ //väidet kontollides tõstetakse ajutine pilt ümber target kataloogi kui õnnestub
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
			unset($photo_upload);
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
	<p><?php echo "Kasutaja: ". $_SESSION["user_firstname"] ." ". $_SESSION["user_lastname"] . ""?></p>
	<ul>
		<li><a href="home.php">Avalehele</a></li>
		<li><a href="gallery.php">Galerii</a></li>
		<li><a href="?logout=1">Logi välja</a></li>
	</ul>
	<hr>
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
		<label for="file_input">Vali foto endale: </label>
		<input id="file_input" name="file_input" type="file">
		<br>
		<label for="alt_input">Alternatiivtekst ehk pildi selgitus: </label>
		<input id="alt_text" name="alt_text" type="text" placeholder="Pildil on ...">
		<br>
		<label for="alt_input">Savlesta fail nimega(selle puudumisel genereeritakse nimi automaatselt): </label>
		<input id="save_name" name="save_name" type="text">
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





	