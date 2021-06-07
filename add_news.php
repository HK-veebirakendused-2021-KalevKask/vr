<?php

	require_once "usesession.php";
	//require_once "usesession.php";		// ainult sisseloginud kasutajale
	require_once "../../../conf.php"; //annab andmbaasiga ühendamiseks vajalikud andmed, mis asuvad antud asukohas tigur serversis	
	require_once "fnc_general.php";	//valideerimis funktsioonid, w3schools materjalides. aitab korrigeerida sisendiks saadud teksti
	$news_input_error = null; //tekitame muutuja, millele tekib väärtus juhul, kui uudise peakiri jääb sisetamata (rida 14)
	//var_dump($_POST);											// On olemas ka $_GET		// näitab kõiki postitusi
	$titleTemp = null; 		// pealkirja väli
	$contentTemp = null; 	// //tekitame muutuja, millele tekib väärtus juhul, kui uudise sisu jääb sisetamata (rida 18)
	$authorTemp = null;		// autori siu
	if(isset($_POST["news_submit"])){   //Kui POST massivi sees on news_submitile on määratud väärtus, ehk kas salvesta uudis nuppu on kasutaja vajutanud 
		if(empty($_POST["news_title_input"])){ //Kui pealkirja lahter on tühi, pärast submit nuppu vajutamist tehakse järggnevad toimingud:
			$news_input_error = "Uudise pealkiri on puudu! ";
			if(isset($_POST["news_content_input"])){
				$contentTemp = $_POST["news_content_input"];
			}
			if(isset($_POST["news_author_input_input"])){
				$authorTemp = $_POST["news_content_input"];
			}
		}
		if(empty($_POST["news_content_input"])){  //Kui sisu lahter on tühi, pärast submit nuppu vajutamist tehakse järggnevad toimingud:
			$news_input_error .= " Uudise tekst on puudu! ";		//.= võta senine ja pane juurde
			if(isset($_POST["news_title_input"])){
				$titleTemp = $_POST["news_title_input"];
			}
			if(isset($_POST["news_author_input"])){
				$authorTemp = $_POST["news_author_input"];
			}
		}
	

		
		if(empty($news_input_error)){	//Kui vajalikjud väljad sisendina antud saab programm edasi toimetama vastavat
			// valideerime sisendandmed
			$news_title_input = test_input($_POST["news_title_input"]); 
			$news_content_input = test_input($_POST["news_content_input"]);
			$news_author_input = test_input($_POST["news_author_input"]);
			//Salvestame andmebaasi
			store_news($_POST["news_title_input"], $_POST["news_content_input"], $_POST["news_author_input"]); //kui lahtrid on täidetud, võetakse store_news funktsioonile sisendiks antud parameetrid POST masiivist
		}
	}
	
	function store_news($news_title, $news_content, $news_author){ // funktsiooni sisenditele kirjeldatakse ära uued muutajate nimed antud reaga
		//echo $news_title .$news_content .$news_author; - //saab kontrollida kas andmed jõuavad antud muutujatesse.
		//echo $GLOBALS["server_host"];  //funktsioonist väljas poolt olevatele muutjuatele tuleb panna ette märge $GLOBALS, et saaks väärtuse kätte

		// loome andmebaasi serveri ja baasiga ühenduse
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]); //sisaldab conf failis olevaid parameetreid
		//määrame suhtluseks kodeeringu
		$conn -> set_charset("utf8");
		// valmistan ette SQL käsu
		$stmt = $conn -> prepare("INSERT INTO vr21_news (vr21_news_news_title, vr21_news_news_content, vr21_news_news_author) VALUES (?,?,?)"); // testime kas funktsioon töötab, sellpärast on valus väärtuseks kolm küsimärki
		echo $conn -> error; //kui on näiteks andmetabeli väli kirjutatud valesti saame siit kätte info. Tegemist on testiga, mitte veel päris andmete saatmisega
		//Nüüd saadame päriselt andmed
		// ?-ga andmete sidumine i-integer, s-string d-decimal, peavad ühtima väljadega
		$stmt -> bind_param("sss", $news_title, $news_content, $news_author); /// ette on kirjutatud kolm s-i, ehk eelevalt kontrollitakse kas antud sisendi andmetüübid vastavad sellele mida andmbaas ootab. Järgnevad sisendid peavad olema samas järjekorras nagu prepare käsus
		$stmt -> execute(); //täitetakse käsk
		$stmt -> close();
		$conn -> close(); //logitakse andmebaasist välja
		//tühjendame muutujate sisu, et neid ei oleks võimalik korduvalt käiku lasta
		$GLOBALS["news_input_error"] = null;
		$GLOBALS["news_title"] = null;
		$GLOBALS["news_content"] = null;
		$GLOBALS["news_author"] = null;
	}


?>

<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="UTF-8">
	<title>Veebirakendused ja nende loomine 2021</title>
</head>
<body>
	<h1>Uudiste lisamine</h1>
	<p>See leht on valminud õppetöö raames!</p>
	<hr>
	<p><?php echo "Kasutaja: ". $_SESSION["user_firstname"] ." ". $_SESSION["user_lastname"] . ""?></p>
	<ul>
		<li><a href="home.php">Avalehele</a></li>
		<li><a href="?logout=1">Logi välja</a></li>
	</ul>
	<hr>
	<p style="color:red"><b><?php echo $news_input_error; ?></b></p> <!-- Kuvab errorid, kui mingi välja jäävad sisestamatta-->
	
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  <!-- Formiga saab võtta infot kasutajalt vastu, mis saadetakse serverisse, method="POST" saadab andmed seriverisse nii. et neid brauseri aadressi real välja ei paista-->
		<label for="news_title_input"><b>Uudise pealkiri: </b></label> <br> <!--elemendi nimetus-->
		<input type="text" id="news_title_input" name="news_title_input" placeholder="Pealkiri" value="<?php echo $titleTemp; ?>"><br> <!-- Sisend mida saab kirjutada antud lahtrisse, labeli nimi on id-->
		<br>
		<label for="news_content_input"><b>Uudise tekst: </b></label> <br>
		<textarea name="news_content_input" id="news_content_input" placeholder="Uudise tekst" rows="6" cols="40"><?php echo $contentTemp; ?></textarea><!-- textarea on samamoodi input ainukese vahega, et siia saab tekti rohkem lisada-->
		<br>
		<label for="news_author_input"><b>Uudise sisestaja nimi: </b>(Kui nime ei sisesta, oled anonüümne)</label> <br>
		<input type="text" id="news_author_input" name="news_author_input" placeholder="Nimi" value="<?php echo $authorTemp; ?>">
		<br>
		<input type="submit" name="news_submit" value="Salvesta uudis!"> <!-- kogub terve vormi sisu ja saadab serverile -->
		<br>
	</form>
	

</body>
</html>