<?php
	require_once "usesession.php";
	require_once "../../../conf.php";
		
	function read_news() { // funktsioon uudiste lugemiseks
		if(isset($_POST["count_submit"])) { // kui kasutaja on valinud uudiste arvu, mida kuvada soovib
		$newsCount = $_POST['newsCount']; // määratakse välja kuvatavte uudiste arv
		}
		else { // kui kasutaja pole uudiste arvu valinud
			$newsCount = 3; // kuvatavate uudiste arv vaikimisi
		}
		//loome andmebaasis serveriga ja baasiga ühenduse
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		//määrame suhtluseks kodeeringu
		$conn -> set_charset("utf8");
		//valmistan ette SQL käsu
		$stmt = $conn -> prepare("SELECT vr21_news_news_title, vr21_news_news_content, vr21_news_news_author, vr21_news_news_added FROM vr21_news ORDER BY vr21_news_id DESC LIMIT ?"); //kilejdame ära mida tahame lugeda ja kust tabelist
		echo $conn -> error;
		$stmt -> bind_result($news_title_from_db, $news_content_from_db, $news_author_from_db, $news_added_from_db); //lisame andmed andmbaasist uutesse muutujatesse
		//i - integer   s - string   d - decimal
		$stmt -> bind_param("i", $newsCount); // edastame uudiste arvu SQL-käsule
		$stmt -> execute();
		$raw_news_html = null; 

	
	

	while ($stmt -> fetch()) { //nii kaua kui andmeid võtta on, tehakse järgmised toimingud
		$raw_news_html .= "\n <h2>" .$news_title_from_db ."</h2>";
		$newsDate = new DateTime($news_added_from_db); // teen andmebaasist võetud kuupäevast dateTime objekti
		$nDate = $newsDate->format('d.m.Y'); // Teisendan dateTime objekti vajalikku formaati
		$raw_news_html .= "\n <p>Lisatud: " . $nDate."</p>"; // Uudiste lisamise kuupäev
		$raw_news_html .= "\n <p>" .nl2br($news_content_from_db) ."</p>"; //nl2br - tähendab newline ja kaks 2br
		$raw_news_html .= "\n <p>Edastas: ";
		if(!empty($news_author_from_db)) { // kui on nimi uudise kirjutajal sisestatud
			$raw_news_html .= $news_author_from_db;
		} else { //kui pole nime siis antakse vastuseks
			$raw_news_html .= "Tundmatu reporter";
		}
		$raw_news_html .= "</p>";
		$raw_news_html .= "\n <hr>";
	}
	$stmt -> close();
	$conn -> close();
	return $raw_news_html; //siin tuleb funktsiooni eead_news lõpp väärtus
}

$news_html = read_news();

?>
<!DOCTYPE html>
<html lang="et">
<head>
<meta charset="utf-8">
<title>Veebirakendused ja nende loomine 2021</title>
</head>
<body>
<h1>Uudiste lugemine</h1>
<p>See leht on valminud õppetöö raames!</p>
<hr>
<p><?php echo "Kasutaja: ". $_SESSION["user_firstname"] ." ". $_SESSION["user_lastname"] . ""?></p>
<ul>
	<li><a href="home.php">Avalehele</a></li>
	<li><a href="?logout=1">Logi välja</a></li>
</ul>
<hr>
<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> <!-- Vorm kuvatavate uudiste arvu määramiseks -->
<input type="number" min="1" max="10" value="3" name="newsCount">
<input type="submit" name="count_submit" value="Kuva uudised">
</form>
<p><?php echo $news_html; ?></p> <!-- Kuvab uudised -->
</body>
</html>