<?php
	//session_start();
	require_once "classes/SessionManager.class.php";
	SessionManager::sessionStart("vr", 0, "/~andrus.rinde/", "tigu.hk.tlu.ee");
	
	require_once "../../../conf.php";
	//require_once "fnc_general.php";
	require_once "fnc_user.php";
	
	//klassi näide
	


	//klassi näide
	//require_once "classes/Test.class.php";
	//$test_object = new Test(5);
	//echo " Avalik number on".$test_object->non_secret;
	//$test_object->reveal();
	
	
	$myname = "Kalev Kask";
	$currenttime = date("d.m.Y H:i:s");
	$timehtml = "\n <p>Lehe avamise hetkel oli: " .$currenttime .".</p> \n";
	$semesterbegin = new DateTime("2021-1-25");
	$semesterend = new DateTime("2021-6-30");
	$semesterduration = $semesterbegin->diff($semesterend);
	$semesterdurationdays = $semesterduration->format("%r%a");
	$semesterdurhtml = "\n <p>2021 kevadsemestri kestus on " .$semesterdurationdays ." päeva.</p> \n";
	$today = new DateTime("now");
	$fromsemesterbegin = $semesterbegin->diff($today);
	$fromsemesterbegindays = $fromsemesterbegin->format("%r%a");
	
	if ($today < $semesterbegin){
		$semesterprogress = "\n <p>Semester pole veel alanud.</p> \n";
	} elseif ($today > $semesterend) {
		$semesterprogress = "\n <p>Semester on lõppenud.</p> \n";
	} else {
		$semesterprogress = "\n"  .'<p>Semester edeneb: <meter min="0" max="' .$semesterdurationdays .'" value="' .$fromsemesterbegindays .'"></meter>.</p>' ."\n";
	}
		
	
	//loeme piltide kataloogi sisu
	$picsdir = "/home/kalev.kask/public_html/vr2021/vr/pics/";
	$allfiles = array_slice(scandir($picsdir), 2);
	//echo $allfiles[5];
	//var_dump($allfiles);
	$allowedphototypes = ["image/jpeg", "image/png"];
	$picfiles = [];
	
	//for($x = 0; $x <10;$++){
		//tegevus
	//}
	foreach($allfiles as $file){
		$fileinfo = getimagesize($picsdir .$file);
		//var_dump($fileinfo);
		if(isset($fileinfo["mime"])){
			if(in_array($fileinfo["mime"], $allowedphototypes)){
				array_push($picfiles, $file);
			}
		}
	}
	
	$photocount = count($picfiles);
	$photonum = mt_rand(0, $photocount-1);
	$randomphoto = $picfiles[$photonum];
	
	$treepicarray = [];
	$i = 1;
	while ($i <= 3) {
		$photonum2 = mt_rand(0, $photocount-1);
		$randomphoto2 = $picfiles[$photonum2];
		if (!in_array($randomphoto2, $treepicarray)) {
			array_push($treepicarray, $randomphoto2);
			$i++;
		}
	}
	
	$weekdays = array( "Pühapäev", "Esmaspäev", "Teisipäev", "Kolmapäev", "Neljapäev", "Reede", "Laupäev",);
	$dayEST = $weekdays[date_format($today, 'w')];
	
	//sisselogimine
	$notice2 = null;
	$notice = null;
	$email = null;
	$email_error = null;
	$password_error = null;
	
	if(isset($_POST["login_submit"])){//kui on vajutatuud submit mida teha tuleb
		//kontrollime kas email ja passeord on põhimõtteliselt olemas
		
		$notice = sign_in($_POST["email_input"], $_POST["password_input"]);
		if($notice == 1){
			$notice = "Saate sisse logia";
		} else {
			$notice = "Sisselogimine ebaõnnestus, viga kasutajatunnuses või paroolis.";
		}
		
	}
	

	
	if(isset($_POST["login_test"])){//kontrollin fetchi
		$notice2 = find_fetch($_POST["email_input_test"]);
	}
	
?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebirakendused ja nende loomine 2021</title>
</head>
<body>
	<h1>
	<?php
		echo $myname;
	?>
	</h1>
	<p>See leht on valminud õppetöö raames!</p>
	<hr>
		<h2>Logi sisse</h2>
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<label>E-mail (kasutajatunnus):</label><br>
		<input type="email" name="email_input" value="<?php echo $email; ?>"><span><?php echo $email_error; ?></span>
		<br>
		<label>Salasõna:</label><br>
		<input name="password_input" type="password"><span><?php echo $password_error; ?></span><br>
		<input name="login_submit" type="submit" value="Logi sisse!"><span><?php echo $notice; ?></span>
	</form>
	<p>Loo endale <a href="add_user.php"> kasutajakonto!</a></p>
	<?php
		echo $timehtml. " Täna on ".$dayEST;
		echo $semesterdurhtml;
		echo $semesterprogress;
	?>

	<br>
	<p><b>3 juhusliku pilti kogu Haapsalu pildivaramust</b></p>
	<img src="<?php echo "https://tigu.hk.tlu.ee/~kalev.kask/vr2021/vr/pics/$treepicarray[0] \n"; ?>" alt="juhuslik pilt 1">
	<img src="<?php echo "https://tigu.hk.tlu.ee/~kalev.kask/vr2021/vr/pics/$treepicarray[1] \n"; ?>" alt="juhuslik pilt 2">
	<img src="<?php echo "https://tigu.hk.tlu.ee/~kalev.kask/vr2021/vr/pics/$treepicarray[2] \n"; ?>" alt="juhuslik pilt 3">
	
</body>
</html>