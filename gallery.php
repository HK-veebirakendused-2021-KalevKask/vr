<?php
	require_once "../../../conf.php";
	require_once "usesession.php";
	require_once "fnc_user.php";
	require_once "classes/Upload_photo.class.php";
	require_once "fnc_photo.php";

	$thumb_picsdir = "../upload_photos_thumbnail/";
	$norm_picdir = " ../upload_photos_normal/";

	
	

?>
	<!DOCTYPE html>
	<html lang="et">
	<head>
		<meta charset="utf-8">
		<title>Veebirakendused ja nende loomine 2021</title>
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Limelight&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">  
		<link rel="stylesheet" href="style.css">
	</head>
	<body>
		<h1>Galerii</h1>
		<h3>
		<?php
			echo "Nimi: ". $_SESSION["user_firstname"] ." ". $_SESSION["user_lastname"] . "";
		?>
		</h3>
		<hr>
		<ul>
			<li><a href="home.php">Avalehele</a></li>
			<li><a href="?logout=1">Logi välja</a></li>
		</ul>
		<hr>

	
<?php
	echo display_gallery(3, $thumb_picsdir); //Esimene sisend on privacy aste ja teine on pildi asukoha kaust

	

?>




	
</body>	
</html>
