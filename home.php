<?php
require_once "fnc_user.php";
require_once "usesession.php";
	// session_start();
	// //kas on sisse loginud
	// if(!isset($_SESSION["user_id"])){//kui kasutaja pole sisse loginud
		// header("Location: page.php"); // suunatakse kasutaja page leheküljele
	// }
	//Välja logimine
	// if(isset($_GET["logout"])){ //kui kasutaja logib välja
		// session_destroy(); //sessioon katkestatakse
		// header("Location: page.php");  //kasutaja suunatakse pahe.php-sse tagasi
		
	// }
//var_dump($_SESSION);

?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebirakendused ja nende loomine 2021</title>
</head>
<body>
	
	<h1>Sisseloginud kasutaja, vinge süsteem</h1>
	<p>See leht on valminud õppetöö raames!</p>
	<hr>
	<p><?php echo "Kasutaja: ". $_SESSION["user_firstname"] ." ". $_SESSION["user_lastname"] . ""?></p>
	<ul>	
		<li><a href="add_news.php">Uudiste lisamine</a></li>
		<li><a href="show_news.php">Uudise lugemine</a></li>
		<li><a href="upload_photo.php">Fotode üles laadimine</a></li>
		<li><a href="gallery.php">Galerii</a></li>
		<li><a href="?logout=1">Logi välja</a></li>
	</ul>
	<hr>
</body>
</html>